"""Native WordPress HTTP checks. Requires beautifulsoup4 and isolated loopback WP."""
import csv, http.cookiejar, io, json, os, pathlib, re, secrets, subprocess, urllib.error, urllib.parse, urllib.request
from bs4 import BeautifulSoup
ROOT=pathlib.Path(__file__).resolve().parent
BASE=os.environ.get('SND_TEST_URL','http://127.0.0.1:8899').rstrip('/')
if urllib.parse.urlparse(BASE).hostname not in ('127.0.0.1','localhost'):raise SystemExit('Refusing non-loopback site.')
ENV={**os.environ,'SND_TEST_URL':BASE};APP=BASE+'/srbija-na-dlanu/';count=0
def check(value,label):
 global count
 count+=1
 if not value:raise AssertionError(label)
def fixture(op,**args):
 r=subprocess.run([os.environ.get('PHP_BIN','php'),str(ROOT/'http-fixture.php')],input=json.dumps({'op':op,**args}),capture_output=True,text=True,env=ENV,check=True)
 return json.loads(r.stdout) if r.stdout.strip() else None
class Client:
 def __init__(self):self.jar=http.cookiejar.CookieJar();self.opener=urllib.request.build_opener(urllib.request.ProxyHandler({}),urllib.request.HTTPCookieProcessor(self.jar))
 def get(self,path,body=None,headers=None):
  req=urllib.request.Request(urllib.parse.urljoin(BASE,path),data=body,headers=headers or {})
  try:
   with self.opener.open(req,timeout=30) as r:return r.status,r.url,r.headers,r.read().decode(errors='replace')
  except urllib.error.HTTPError as e:return e.code,e.url,e.headers,e.read().decode(errors='replace')
 def form(self,path,fields,selector='.snd-auth-card form'):
  status,url,h,html=self.get(path);s=BeautifulSoup(html,'html.parser');f=s.select_one(selector);check(status==200 and f is not None,'Native form available')
  values={i['name']:i.get('value','') for i in f.select('input[type=hidden]') if i.get('name')};values.update(fields)
  return self.get(f['action'],urllib.parse.urlencode(values,doseq=True).encode(),{'Content-Type':'application/x-www-form-urlencoded','Origin':BASE})
 def session(self):return json.loads(self.get('/wp-admin/admin-ajax.php?action=snd_session')[3])
 def api(self,path,body,lang='en'):
  s=self.session();h={'Content-Type':'application/json','X-SND-Theme':'dark'}
  if s['authenticated']:h['X-WP-Nonce']=s['nonce']
  status,url,headers,raw=self.get('/wp-json/srbija-na-dlanu/v1/'+path+'?lang='+lang,json.dumps(body).encode(),h);return status,json.loads(raw)
 def move(self,g,action,value=None,lang='en'):
  status,data=self.api('action',{'token':g['token'],'revision':g['state']['revision'],'action':action,'value':value},lang);check(status==200,'Localized action '+action);return data
password=secrets.token_urlsafe(30);fixture('setup',password=password)
try:
 guest=Client()
 for lang,title in [('sr','Baza znanja'),('en','Knowledge base'),('de','Wissensdatenbank'),('ru','База знаний')]:
  for path in ('','okruzi/','opstine/','baza-znanja/','prijava/'):
   status,url,h,html=guest.get(APP+path+'?lang='+lang);s=BeautifulSoup(html,'html.parser')
   check(status==200 and not re.search(r'<b>(Warning|Notice|Fatal error)',html),'Clean localized page '+lang+' '+path)
   check(s.html['lang']==('sr-Latn' if lang=='sr' else lang),'HTML locale');check('no-store' in h.get('Cache-Control',''),'Locale page uncached')
   check(bool(s.select_one('.snd-app-header .snd-compass')) and len(s.select('.snd-app-nav a'))==5,'Standalone header and compass')
   check(len(s.select('a[data-language]'))==4 and len(s.select('button[data-snd-theme-choice]'))==3,'Four languages and three themes')
   check('themes.css?ver=3.2.0' in html and 'ui.js?ver=3.2.0' in html,'Theme and preferences assets loaded')
   check(len(s.select('link[hreflang]'))==4 and len(s.head.select('title'))==1,'Localized metadata without duplicate title')
   check(all('lang='+lang in a.get('href','') for a in s.select('.snd-app-nav a')),'Menu preserves language')
   if path=='baza-znanja/':check(s.h1.get_text()==title and len(s.select('.snd-district-card'))==30,'Knowledge base translated with all districts')
  status,url,h,html=guest.get(APP+'baza-znanja/?lang='+lang+'&okrug=rs-12');s=BeautifulSoup(html,'html.parser');check(len(s.select('.snd-explorer-map .snd-explorer-outline'))==1 and len(s.select('.snd-member-table tbody tr'))>0,'Localized district details retain shape and members')
  status,g=guest.api('start',{'kind':'municipality','mode':'practice','fresh':True},lang);check(status==200,'Localized game starts');g=guest.move(g,'giveup',lang=lang);g=guest.move(g,'next',lang=lang)
  while g['state']['stage']=='quiz':
   q=g['state']['question'];check('correct' not in q and 'explanation' not in q,'No answer leak in localized question');check(lang=='sr' or not q['title'].startswith(('Koja','Koje','Koliko','Kojem','U kom','Na ušću')),'Localized question text');g=guest.move(g,'skip',lang=lang);g=guest.move(g,'next',lang=lang)
  check(g['state']['stage']=='complete' and len(g['state']['review'])==4,'Localized complete review')
 status,url,h,html=guest.get(APP+'?lang=de&snd_theme=dark');cookies={c.name:c.value for c in guest.jar};check(cookies.get('snd_lang')=='de' and cookies.get('snd_theme')=='dark','Native preference cookies persist')
 status,url,h,html=guest.get(APP+'baza-znanja/?lang=en&pretraga=Ra%C5%A1ka');s=BeautifulSoup(html,'html.parser');check(len([a for a in s.select('.snd-district-card') if not a.has_attr('hidden')])==1,'Translated knowledge search works server-side')
 a=Client();status,url,h,html=a.form(APP+'prijava/?lang=en',{'login':'snd-http-a','password':password});check('/nalog/' in url and 'lang=en' in url and a.session()['authenticated'],'English native login and account redirect');check('My account' in html,'Localized account page')
 status,g=a.api('start',{'kind':'district','mode':'practice','fresh':True});g=a.move(g,'giveup');g=a.move(g,'finish');check(g['progress']['district']['practice_count']==1,'Account result recorded')
 second=Client();second.form(APP+'prijava/?lang=ru',{'login':'snd-http-a','password':password});check(second.session()['progress']['district']['practice_count']==1,'Progress survives language and device change');check(second.api('resume',{'token':g['token']},'ru')[0]==200,'Same account can resume in another language')
 outsider=Client();outsider.form(APP+'prijava/?lang=de',{'login':'snd-http-b','password':password});check(outsider.api('resume',{'token':g['token']},'de')[0]==403,'Other account denied');check(outsider.get('/wp-admin/admin.php?page=snd-players')[0]==403,'Player administration denied to subscriber')
 admin=Client();admin.form(APP+'prijava/?lang=sr',{'login':'snd-http-admin','password':password})
 for page in ('analytics','players','games','settings','translations'):
  status,url,h,html=admin.get('/wp-admin/admin.php?page=snd-'+page);check(status==200 and 'snd-control' in html and not re.search(r'<b>(Warning|Fatal error)',html),'Actual admin page '+page+' status='+str(status)+' '+BeautifulSoup(html,'html.parser').get_text(' ',strip=True)[-500:])
 status,url,h,html=admin.get('/wp-admin/admin.php?page=snd-settings');s=BeautifulSoup(html,'html.parser');form=s.select_one('form input[value=snd_control_save]').parent
 fields={i['name']:i.get('value','') for i in form.select('input') if i.get('name') and (i.get('type')!='checkbox' or i.has_attr('checked'))};fields.update({'settings[languages][]':['sr','en','de','ru'],'settings[rounds][]':[],'settings[max_guesses]':'3','settings[hint_after]':'1','settings[default_theme]':'dark','settings[default_language]':'en'})
 fields.pop('settings[rounds][]',None);status,url,h,html=admin.get(form['action'],urllib.parse.urlencode(fields,doseq=True).encode(),{'Content-Type':'application/x-www-form-urlencoded','Origin':BASE});check(status==200 and 'saved=1' in url,'Settings save with real nonce');check(fixture('settings')['max_guesses']==3,'Admin settings persisted')
 fields['_wpnonce']='invalid';check(admin.get(form['action'],urllib.parse.urlencode(fields,doseq=True).encode(),{'Content-Type':'application/x-www-form-urlencoded','Origin':BASE})[0]==403,'Invalid settings nonce denied')
 status,new=guest.api('start',{'kind':'district','mode':'practice','fresh':True});check(new['state']['max_guesses']==3 and new['state']['question_count']==0,'New rules reach actual HTTP game');new=guest.move(new,'giveup');new=guest.move(new,'next');check(new['state']['stage']=='complete','No-quiz game finishes over HTTP')
 status,url,h,html=admin.get('/wp-admin/admin.php?page=snd-analytics&from=2000-01-01');s=BeautifulSoup(html,'html.parser');exports=[a['href'] for a in s.select('.snd-exports a')];check(len(exports)==6,'CSV and JSON exports for three datasets')
 for link in exports:
  status,url,h,raw=admin.get(link);check(status==200 and 'attachment' in h.get('Content-Disposition',''),'Authorized export download')
  if 'format=json' in link:check(isinstance(json.loads(raw),list),'JSON export valid')
  else:check(len(list(csv.reader(io.StringIO(raw.lstrip('\ufeff')))))>=1,'CSV export valid')
  check(outsider.get(link)[0]==403,'Export denied to subscriber')
 reg=Client();login='snd-http-new-'+secrets.token_hex(5);status,url,h,html=reg.form(APP+'prijava/?mode=register&lang=ru',{'login':login,'email':login+'@example.invalid'});check('notice=registered' in url,'Russian registration');mail=fixture('mail');check('Добро пожаловать' in mail['message'],'Russian native registration email');link=next(u for u in re.findall(r'https?://[^\s<>]+',mail['message']) if 'key=' in u);check(link.count('?')==1 and 'lang=ru' in link,'Valid localized reset link');status,url,h,html=reg.get(link);check('mode=reset' in url and 'key=' not in url,'Reset key stripped into HTTP-only cookie');new_password=secrets.token_urlsafe(30);status,url,h,html=reg.form(url,{'password':new_password,'password_confirm':new_password});check('notice=password-changed' in url,'Localized password reset succeeds')
 print(f'PASS: {count} version 3 HTTP assertions; four languages, native auth, admin saves, exports and cross-device privacy.')
finally:fixture('cleanup')
