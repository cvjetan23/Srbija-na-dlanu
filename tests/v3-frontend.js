'use strict';
// State, event and generated-markup checks; no browser layout claims.
const assert=require('node:assert/strict'),fs=require('node:fs'),vm=require('node:vm');
const raw=fs.readFileSync(__dirname+'/../srbija-na-dlanu/data/i18n.php','utf8');
const data=JSON.parse(raw.match(/SND_LANGUAGES'\n([\s\S]*?)\nSND_LANGUAGES/)[1]);
const L=require('../srbija-na-dlanu/assets/i18n.js');let total=0;
function check(value,label){assert.ok(value,label);total++;}
global.document={querySelectorAll:()=>[],documentElement:{dataset:{sndTheme:'dark'}}};
global.location={href:'https://example.test/srbija-na-dlanu/okruzi/?lang=en'};
const {Game}=require('../srbija-na-dlanu/assets/game.js');
const labels={sr:['Prijava','1 dan'],en:['Sign in','1 day'],de:['Anmelden','1 Tag'],ru:['Вход','1 день']};
for(const lang of Object.keys(labels)){
 const dictionary=Object.fromEntries(Object.entries(data).map(([key,value])=>[key,value[lang]||value.sr||key]));L.configure({language:lang,dictionary});
 check(L.t('Prijava')===labels[lang][0],'Sign-in translation '+lang);check(L.days(1)===labels[lang][1],'Day grammar '+lang);
 const html=L.html('<label title="Prijava">Prijava</label><a href="/?q=Prijava">Prijava</a><script>Prijava</script>');
 check(html.includes('/?q=Prijava')&&html.includes('<script>Prijava</script>'),'Translation preserves URL and script '+lang);
 check(html.includes('>'+labels[lang][0]+'</label>'),'Text-node translation '+lang);
 check(!L.text('Pokušaj 2 od 6').includes('{'),'Dynamic placeholder filled '+lang);
 const g=Object.create(Game.prototype);g.state={kind:'district',mode:'practice',stage:'guess',question_count:0,max_guesses:3,hint_after:1,hint_enabled:false,guesses:[{id:'a',name:'A',km:3,direction:{arrow:'↑',name:'North'}}],hint:false};g.root={id:'v3'};
 check(!g.guessPanel().includes('data-cmd="hint"'),'Disabled help hidden '+lang);g.state.hint_enabled=true;check(g.guessPanel().includes('data-cmd="hint"'),'Custom hint threshold '+lang);
 g.state={...g.state,stage:'reveal',territory:{name:'A'},solved:false};check(g.panel().includes('<span>Rezultat</span>'),'Empty quiz leads to result '+lang);
}
L.configure({language:'en',dictionary:{'Prijava':'<img src=x onerror=alert(1)>','Pogođeno iz {count}. pokušaja.':'Attempt {count}.'}});
check(L.html('<p>Prijava</p>').includes('&lt;img'),'Translation rendered as escaped text');check(L.text('Pogođeno iz 3. pokušaja.')==='Attempt 3.','Dynamic result text');
check(L.html('<p data-snd-original>Prijava</p>')==='<p data-snd-original>Prijava</p>','Names marked original are preserved');
async function run(){
 let request;global.fetch=async(url,options)=>{request={url,options};return {ok:true,json:async()=>({ok:true})};};
 const game=Object.create(Game.prototype);game.api='https://example.test/wp-json/srbija-na-dlanu/v1/';game.root={dataset:{lang:'en'}};game.account={authenticated:true,nonce:'test-nonce'};
 await game.request('resume',{token:'opaque'});check(new URL(request.url).searchParams.get('lang')==='en','Locale propagated to API');check(request.options.headers['X-SND-Theme']==='dark'&&request.options.headers['X-WP-Nonce']==='test-nonce','Theme and WP nonce propagated');
 const events={},buttons=['light','dark','auto'].map(choice=>({dataset:{sndThemeChoice:choice},attrs:{},setAttribute(k,v){this.attrs[k]=v;},addEventListener(type,fn){this[type]=fn;}}));
 const summary={focus(){this.focused=true;}},picker={open:true,addEventListener(type,fn){this[type]=fn;},querySelector:()=>summary,contains(target){return target===this;}};
 const document={getElementById:()=>null,documentElement:{dataset:{sndPreference:'auto'},classList:{add(){},remove(){}}},visibilityState:'visible',hasFocus:()=>true,cookie:'',referrer:'',addEventListener(type,fn){(events[type]??=[]).push(fn);},querySelector:()=>null,querySelectorAll:s=>s==='[data-snd-theme-choice]'?buttons:s==='.snd-language-picker'?[picker]:[]};
 let mediaChange;const media={matches:true,addEventListener(type,fn){mediaChange=fn;}};const stored=new Map();let interval;
 const context={window:{addEventListener(){},SND_L10N:{config:()=>({theme:'auto',theme_switch:true,analytics:false,cookie_path:'/'})},matchMedia:()=>media},document,localStorage:{setItem:(k,v)=>stored.set(k,v)},location:{protocol:'https:'},URL,Date,setTimeout:()=>1,clearTimeout(){},innerWidth:375,setInterval:fn=>{interval=fn;}};
 vm.runInNewContext(fs.readFileSync(__dirname+'/../srbija-na-dlanu/assets/ui.js','utf8'),context);
 const click=button=>button.click({preventDefault(){}});
 check(document.documentElement.dataset.sndTheme==='dark','System dark mode applied');click(buttons[0]);check(document.documentElement.dataset.sndTheme==='light'&&stored.get('snd:theme')==='light','Sun selects and persists light');check(document.cookie.includes('Secure')&&document.cookie.includes('SameSite=Lax'),'Theme cookie scoped and secure');click(buttons[1]);check(document.documentElement.dataset.sndTheme==='dark'&&buttons[1].attrs['aria-pressed']==='true'&&buttons[0].attrs['aria-pressed']==='false','Moon selects dark with accessible state');click(buttons[2]);media.matches=false;mediaChange();check(document.documentElement.dataset.sndTheme==='light','System theme follows device changes');picker.keydown({key:'Escape'});check(!picker.open&&summary.focused,'Escape closes language picker and restores focus');picker.open=true;events.click.forEach(fn=>fn({target:{}}));check(!picker.open,'Outside click closes language picker');
 const css=fs.readFileSync(__dirname+'/../srbija-na-dlanu/assets/themes.css','utf8');check(css.includes('color-scheme:dark')&&css.includes('snd-option.is-wrong')&&css.includes('--snd-button-bg')&&css.includes('--snd-button-radius'),'Dark game, feedback colors and shared action component present');
 console.log('PASS: '+total+' version 3 frontend assertions; localization, API language, custom rules and theme events.');
}
run().catch(error=>{console.error(error);process.exitCode=1;});
