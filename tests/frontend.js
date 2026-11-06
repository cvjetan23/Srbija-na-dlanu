'use strict';
// Run with Node; verifies generated markup/request flows, not browser layout.
const assert = require('node:assert/strict');
const values = new Map();
global.localStorage = { getItem:k=>values.get(k)||null,setItem:(k,v)=>values.set(k,v),removeItem:k=>values.delete(k) };
global.document = { querySelectorAll:()=>[] };
global.location = {href:'https://example.test/srbija-na-dlanu/okruzi/'};
const {Game,normalize} = require('../srbija-na-dlanu/assets/game.js');
const checks=[];
function check(ok,message){assert.ok(ok,message);checks.push(message);}
const base={date:'2026-11-06',kind:'district',mode:'daily',stage:'guess',shape:'M0 0L10 0L10 10Z',max_guesses:6,guesses:[],revision:0,question_count:4,question_index:0,score:0,catalog:[]};
const info={date:base.date,counts:{district:30,municipality:174},outline:[],sources:[],coverage:'Sa Kosovom i Metohijom.'};
function root(kind='district'){return {id:'test-game',dataset:{api:'https://example.test/wp-json/srbija-na-dlanu/v1/',ajax:'https://example.test/wp-admin/admin-ajax.php',kind,login:'https://example.test/prijava/',account:'https://example.test/nalog/'},innerHTML:'',setAttribute(){},removeAttribute(){},querySelectorAll(){return [];},querySelector(){return null;}};}
async function run(){
 for(const name of ['Ђаковица','Đakovica','Djakovica'])check(normalize(name)==='djakovica','Cyrillic/Latin normalization: '+name);
 const calls=[];
 const guest={authenticated:false,user:null,nonce:'',progress:null,active:{}};
 const progress={district:{discovered:[],streak:2},municipality:{discovered:[],streak:0}};
 let session=guest,claim=false,nonceRequired=false;
 global.fetch=async(url,options={})=>{
  const route=url.includes('admin-ajax.php')?'session':url.split('/').pop(),body=options.body?JSON.parse(options.body):null;
  calls.push({route,body,headers:options.headers,credentials:options.credentials,cache:options.cache});
  if(nonceRequired&&route!=='session')assert.equal(options.headers['X-WP-Nonce'],'nonce-test');
  let data;
  if(route==='session')data=session;
  else if(route==='bootstrap')data=info;
  else if(route==='claim'){check(body.token==='guest-token','Only stored guest token is claimed');claim=true;data={token:'account-token',state:{...base},progress};}
  else data={token:'guest-token',state:{...base,kind:body && body.kind || base.kind},...(session.authenticated?{progress}:{})};
  return {ok:true,json:async()=>data};
 };
 const host=root(),g=new Game(host);await g.ready;
 check(g.state.stage==='guess','Guest boot completes');
 check(host.innerHTML.includes('<h1>Okruzi</h1>')&&!host.innerHTML.includes('data-cmd="municipality"'),'Dedicated game has fixed heading and no duplicate kind tabs');
 check(host.innerHTML.indexOf('snd-visual')<host.innerHTML.indexOf('snd-question-heading'),'Silhouette above question');
 check(host.innerHTML.includes('Igraš kao gost'),'Guest save status');
 check(calls[0].route==='session'&&calls[1].route==='bootstrap','WP cookie session checked before starting game');
 check(calls.every(c=>c.credentials==='same-origin'&&c.cache==='no-store'),'Requests carry same-origin credentials and bypass cache');
 values.set('snd:v1:preferences',JSON.stringify({kind:'district',mode:'daily'}));
 const m=new Game(root('municipality'));await m.ready;check(m.kind==='municipality','Municipality page starts correct game');
 check(calls.filter(c=>c.route==='start').at(-1).body.kind==='municipality','Municipality page overrides stored district preference');
 session={authenticated:true,user:{id:42,name:'Local tester'},nonce:'nonce-test',progress,active:{}};nonceRequired=true;
 const a=new Game(root());await a.ready;
 check(claim&&a.token==='account-token','Signing in claims guest session through API');
 check(!values.has('snd:v1:session:v2:guest:daily:district:'+base.date),'Claimed guest key removed');
 check(values.has('snd:v1:session:v2:user-42:daily:district:'+base.date),'Account token has user-specific storage key');
 check(a.root.innerHTML.includes('Progres se čuva na nalogu'),'Account save status');
 a.state={...base,stage:'quiz',territory:{name:'Beograd'},question:{type:'flag',title:'Koja je zastava?',options:['a','b','c','d'].map((id,i)=>({id,label:'Zastava '+String.fromCharCode(65+i),image:'https://example.test/flags/'+id+'.png'}))}};
 let markup=a.quizPanel();check((markup.match(/<img /g)||[]).length===4,'Flag question renders four images');
 check(markup.includes('snd-flag-option')&&markup.includes('snd-flag-options'),'Flag options use grid classes');
 check(!markup.includes('is-correct')&&!markup.includes('Autori i licence'),'Unanswered question does not reveal correct choice');
 a.state.question={...a.state.question,correct:'b',explanation:'Zastava Beograda.',source:'https://example.test/source',answer:{choice:'a',correct:false},credits:[{label:'Zastava B',credit:'Autor',license:'CC BY-SA 4.0',source:'https://example.test/source'}]};
 markup=a.quizPanel();check((markup.match(/disabled/g)||[]).length===4&&markup.includes('is-correct')&&markup.includes('is-wrong'),'Answered flags locked and correct/wrong choices marked');
 check(markup.includes('Autori i licence')&&markup.includes('Autor'),'Attribution appears after answer');
 a.state={...base,stage:'complete',territory:{name:'Beograd'},solved:true,review:[]};progress.district.discovered=['rs-00'];
 check(a.resultPanel().includes('Upoznato: 1/30')&&a.streak()===2,'Result uses account progress from server');
 await a.start(true);check(calls.filter(c=>c.route==='start').at(-1).body.fresh===true,'Fresh practice/start flag sent to server');
 check(!values.has('snd:v1:stats'),'Account results never overwrite guest local statistics');
 console.log('PASS: '+checks.length+' frontend assertions; request/session flow and generated markup (no browser).');
}
run().catch(e=>{console.error(e);process.exit(1);});
