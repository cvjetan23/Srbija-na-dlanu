'use strict';
// Functional event/state regressions. No layout or physical-device claims.
const assert=require('node:assert/strict');
const values=new Map();
global.localStorage={getItem:k=>values.get(k)||null,setItem:(k,v)=>values.set(k,v),removeItem:k=>values.delete(k)};
global.document={querySelectorAll:()=>[]};
global.location={href:'https://example.test/srbija-na-dlanu/okruzi/',assign(url){this.assigned=url;}};
const {Game,formatDays,nextSuggestionIndex,touchMoved,kindStats,outcome}=require('../srbija-na-dlanu/assets/game.js');
let total=0;
function check(value,label){assert.ok(value,label);total++;}
function equal(value,expected,label){assert.deepEqual(value,expected,label);total++;}
let serverDate='2026-11-06',serial=0,nextError=null;
const saved=new Map(),calls=[];
const info=()=>({date:serverDate,server_time:Math.floor(Date.now()/1000),next_change:Math.floor(Date.now()/1000)+3600,counts:{district:30,municipality:174},outline:[],coverage:'Srbija sa Kosovom i Metohijom.',sources:[]});
const state=(kind='district',mode='daily')=>({date:serverDate,number:310,kind,mode,stage:'guess',shape:'M0 0L10 0L10 10Z',max_guesses:6,guesses:[],revision:0,question_count:4,question_index:0,score:0,catalog:[{id:'a',name:'A'},{id:'b',name:'B'},{id:'c',name:'C'}]});
const clone=v=>JSON.parse(JSON.stringify(v));
global.fetch=async(url,options={})=>{
 const parsed=new URL(url),route=parsed.pathname.split('/').filter(Boolean).pop(),body=options.body?JSON.parse(options.body):null;
 calls.push({route,body});
 if(route==='admin-ajax.php')return {ok:true,json:async()=>({authenticated:false,user:null,nonce:'',active:[],progress:null})};
 if(route==='bootstrap')return {ok:true,json:async()=>info()};
 if(nextError && route==='action'){const code=nextError;nextError=null;return {ok:false,json:async()=>({code,message:'Test error'})};}
 let data;
 if(route==='start'){const token='token-'+(++serial);data={token,state:state(body.kind,body.mode)};saved.set(token,clone(data));}
 else if(route==='resume' && saved.has(body.token))data=clone(saved.get(body.token));
 else return {ok:false,json:async()=>({code:'snd_expired',message:'Sačuvana partija je istekla.'})};
 return {ok:true,json:async()=>clone(data)};
};
function host(kind='district'){return {id:'qa-game',dataset:{api:'https://example.test/wp-json/srbija-na-dlanu/v1/',ajax:'https://example.test/wp-admin/admin-ajax.php',kind,login:'https://example.test/prijava/',account:'https://example.test/nalog/',districtUrl:'https://example.test/srbija-na-dlanu/okruzi/',municipalityUrl:'https://example.test/srbija-na-dlanu/opstine/'},innerHTML:'',setAttribute(){},removeAttribute(){},querySelectorAll(){return [];},querySelector(selector){return selector==='button'?{}:null;}};}
class Element {
 constructor(){this.events={};this.attrs={};this.hidden=false;this.disabled=false;this.dataset={};this.value='';}
 addEventListener(type,fn){(this.events[type]??=[]).push(fn);}
 dispatch(type,data={}){const e={key:'',button:0,clientX:0,clientY:0,pointerType:'touch',prevented:false,preventDefault(){this.prevented=true;},...data};for(const fn of this.events[type]||[])fn(e);return e;}
 setAttribute(key,value){this.attrs[key]=value;}
 removeAttribute(key){delete this.attrs[key];}
 scrollIntoView(){this.scrolled=true;}
 focus(){if(!this.focused){this.focused=true;this.dispatch('focus');}}
}
class List extends Element {
 set innerHTML(value){this.html=value;this.items=[...value.matchAll(/data-index="(\d+)"/g)].map(m=>{const e=new Element();e.dataset.index=m[1];e.id='qa-game-option-'+m[1];return e;});}
 get innerHTML(){return this.html||'';}
 querySelectorAll(){return this.items||[];}
 querySelector(selector){const m=/data-index="(\d+)"/.exec(selector);return m?this.items[Number(m[1])]:null;}
}
function wireForm(game){
 const input=new Element(),list=new List(),submit=new Element(),form=new Element();
 submit.disabled=true;
 form.querySelector=selector=>selector==='input'?input:selector==='[role=listbox]'?list:submit;
 const root=host();root.querySelector=selector=>selector==='.snd-guess-form'?form:null;game.root=root;game.bind();
 input.focus();return {input,list,submit,form};
}
async function run(){
 for(const [n,label] of [[0,'0 dana'],[1,'1 dan'],[2,'2 dana'],[11,'11 dana'],[21,'21 dan'],[101,'101 dan'],[111,'111 dana']])equal(formatDays(n),label,'Serbian day inflection '+n);
 equal(nextSuggestionIndex(-1,3,'ArrowUp'),2,'First ArrowUp selects last suggestion');
 equal(nextSuggestionIndex(-1,3,'ArrowDown'),0,'First ArrowDown selects first suggestion');
 equal(nextSuggestionIndex(0,3,'ArrowUp'),2,'ArrowUp wraps');
 equal(nextSuggestionIndex(2,3,'ArrowDown'),0,'ArrowDown wraps');
 equal(nextSuggestionIndex(-1,0,'ArrowUp'),-1,'Empty suggestions have no active item');
 check(touchMoved({x:0,y:0},{clientX:0,clientY:20}),'Touch movement is detected');
 check(!touchMoved({x:0,y:0},{clientX:3,clientY:2}),'Short tap is preserved');
 for(const bad of [null,true,15,'wrong',[],{kind:[]},{mode:{}}]){
  values.clear();values.set('snd:v1:preferences',JSON.stringify(bad));
  const g=new Game(host());await g.ready;check(g.state && g.kind==='district' && g.mode==='daily','Invalid preference shape has safe defaults');
 }
 values.clear();values.set('snd:v1:preferences',JSON.stringify({mode:'practice',kind:'municipality'}));
 location.href='https://example.test/srbija-na-dlanu/okruzi/?mode=daily&date=2020-01-01';
 const daily=new Game(host());await daily.ready;
 equal(daily.mode,'daily','Explicit shared mode overrides saved practice');
 equal(daily.kind,'district','Dedicated page wins over saved kind');
 check(daily.notice.includes('Otvoren je današnji izazov'),'Old daily link explains lack of archive');
 const shared=new URL(daily.shareUrl());equal(shared.searchParams.get('mode'),'daily','Shared URL contains mode');equal(shared.searchParams.get('date'),serverDate,'Shared URL contains actual game date');
 values.clear();location.href='https://example.test/srbija-na-dlanu/opstine/?mode=practice';
 const practice=new Game(host('municipality'));await practice.ready;equal(practice.mode,'practice','Explicit practice link is respected');
 // A tab crosses midnight and switches from practice to the daily challenge.
 practice.info.next_change=Math.floor(Date.now()/1000)-1;practice.clockOffset=0;serverDate='2026-11-07';
 await practice.switchGame('municipality','daily');
 const key='snd:v1:session:v2:guest:daily:municipality:'+serverDate;
 check(values.has(key),'Post-midnight token is saved under accepted date');
 equal(JSON.parse(values.get(key)).token,practice.token,'Saved token belongs to new daily game');
 practice.info.date='2026-11-06';practice.accept({token:'transition-token',state:state('municipality','daily')});
 equal(JSON.parse(values.get(key)).token,'transition-token','Accepted state date wins even across response boundary');
 practice.mode='practice';practice.state=state('municipality','practice');
 nextError='snd_expired';await practice.action('guess','a');
 equal(practice.recovery.label,'Nova partija vežbanja','Expired practice offers practice recovery');
 practice.mode='daily';practice.setRecovery({code:'snd_new_day'});equal(practice.recovery.label,'Otvori današnji izazov','Midnight offers daily recovery');
 values.set(practice.key(false,practice.state.date),JSON.stringify({token:'private-token'}));
 practice.setRecovery({code:'rest_cookie_invalid_nonce'});equal(practice.recovery.command,'renewsession','Expired sign-in offers session renewal');
 check(values.has(practice.key(false,practice.state.date)),'Session renewal preserves the saved account token');
 // Exercise the actual input event bindings, including scrolling versus tapping.
 const game=Object.create(Game.prototype);game.state=state();game.selected=null;game.account={authenticated:false};
 const ui=wireForm(game);ui.input.dispatch('keydown',{key:'ArrowUp'});equal(ui.input.attrs['aria-activedescendant'],'qa-game-option-2','Actual ArrowUp handler points at last option');
 ui.input.dispatch('keydown',{key:'Enter'});equal(game.selected.id,'c','Enter confirms last highlighted option');check(ui.list.hidden,'Keyboard choice closes suggestions');
 ui.input.value='';ui.input.dispatch('input');
 const touch=ui.list.dispatch('pointerdown',{clientX:10,clientY:10,pointerType:'touch'});check(!touch.prevented,'Touch panning is not blocked');
 ui.list.dispatch('pointermove',{clientX:10,clientY:60});ui.list.dispatch('pointerup');ui.list.items[1].dispatch('click');
 equal(game.selected,null,'Scrolling must not select a territory');check(!ui.list.hidden,'Scrolling leaves the list open');
 ui.list.dispatch('pointerdown',{clientX:10,clientY:10,pointerType:'touch'});ui.list.dispatch('pointerup');ui.list.items[1].dispatch('click');
 equal(game.selected.id,'b','A short tap selects the intended territory');check(ui.list.hidden,'Tap closes list after focus restoration');
 ui.input.value='';ui.input.dispatch('input');ui.list.dispatch('pointerdown');ui.list.dispatch('pointercancel');ui.list.items[0].dispatch('click');
 equal(game.selected,null,'Cancelled touch does not choose a territory');
 game.state.guesses=[{id:'b',name:'B'}];ui.input.value='B';ui.input.dispatch('input');check(ui.list.innerHTML.includes('već pokušao'),'Already-used name has a specific explanation');
 const reviewer=Object.create(Game.prototype);reviewer.state={...state(),stage:'quiz',territory:{name:'Beograd'},question:{type:'flag',title:'Koja je zastava?',options:['a','b','c','d'].map(id=>({id,label:'Zastava '+id,image:'https://example.test/'+id+'.png'}))}};reviewer.root=host();reviewer.mediaAttempt=0;
 let markup=reviewer.quizPanel();equal((markup.match(/alt=""/g)||[]).length,4,'Flag buttons have decorative images and one accessible label');
 const wrong={title:'Koje je sedište?',type:'seat',choice_label:'Pančevo',correct_label:'Sremska Mitrovica',explanation:'Sedište okruga.',answer:{correct:false,choice:'bad'}};
 markup=reviewer.reviewItem(wrong);check(markup.includes('Koje je sedište?')&&markup.includes('Tvoj odgovor: Pančevo')&&markup.includes('Tačan odgovor: Sremska Mitrovica'),'Review explains question, user choice and correct answer');
 check(!markup.includes('× Sremska Mitrovica'),'Correct answer has no misleading wrong marker');
 markup=reviewer.reviewItem({...wrong,answer:{correct:false,skipped:true}});check(markup.includes('Preskočeno')&&!markup.includes('>Netačno<'),'Skipped question is distinct from a wrong answer');
 markup=reviewer.reviewItem({...wrong,type:'flag',correct_image:'https://example.test/flag.png'});check(markup.includes('src="https://example.test/flag.png"'),'Correct flag survives final review rendering');
 reviewer.state.question={type:'landmark',title:'Znamenitost?',image:'https://example.test/photo.jpg',credit:'Fotograf',license:'CC BY-SA 4.0',options:[]};
 markup=reviewer.visual();check(markup.includes('Fotograf')&&markup.includes('creativecommons.org'),'Attribution stays visible before answer');check(markup.includes('Prikaži izvor fotografije (pomoć)'),'Photo source is explicitly an assisted action before answer');
 const images=[{hidden:false}],buttons=[{disabled:false}],note={hidden:true};
 reviewer.root={querySelectorAll:s=>s==='.snd-media-img'?images:buttons,querySelector:()=>note};reviewer.mediaProblem();
 check(images[0].hidden && buttons[0].disabled && !note.hidden,'Failed image blocks unfair guessing and shows recovery');
 const complete={...state(),stage:'complete',territory:{name:'SECRET-TARGET'},review:[],solved:false};
 equal(outcome({...complete,guesses:[]}), 'Odgovor je otkriven','Voluntary reveal has explicit outcome');
 equal(outcome({...complete,guesses:Array(6).fill({})}), 'Nije pogođeno za šest pokušaja','Six misses have distinct outcome');
 equal(outcome({...complete,solved:true}), 'Pogođeno','Solved outcome is explicit');
 values.set('snd:v1:stats',JSON.stringify({district:{days:null,discovered:5}}));daily.state={...complete,territory:{id:'rs-test',name:'Test'}};daily.kind='district';daily.mode='daily';daily.record();check(Array.isArray(kindStats(JSON.parse(values.get('snd:v1:stats')),'district').days),'Malformed nested statistics recover safely');
 daily.state={...complete,solved:true,guesses:[{correct:true}],territory:{name:'SECRET-TARGET'},review:[{answer:{correct:true}}]};daily.root=host();daily.fixedKind='district';daily.modal=()=>{};
 let copied='';Object.defineProperty(global,'navigator',{value:{clipboard:{async writeText(s){copied=s;}}},configurable:true});
 await daily.share();check(copied.includes('Dnevni izazov #310 · '+serverDate),'Shared text preserves spaces around dynamic values');check(copied.includes('\nKviz 🟩 0/4\n'),'Shared result preserves line breaks and word spaces');check(!copied.includes('SECRET-TARGET'),'Shared result never reveals target');check(copied.includes('?mode=daily&date='),'Shared result has deterministic daily link');

 const fs=require('node:fs'),path=require('node:path');
 const styles=['game.css','app.css'].map(file=>fs.readFileSync(path.join(__dirname,'../srbija-na-dlanu/assets',file),'utf8')).join('\n');
 function lastColor(selector){let color='';for(const rule of styles.matchAll(/([^{}]+)\{([^{}]+)\}/g)){if(rule[1].includes(selector)){const found=/\bcolor:(#[0-9a-f]{6})\b/i.exec(rule[2]);if(found)color=found[1];}}return color;}
 function luminance(hex){const channels=[1,3,5].map(i=>parseInt(hex.slice(i,i+2),16)/255).map(c=>c<=.04045?c/12.92:((c+.055)/1.055)**2.4);return channels.reduce((sum,c,i)=>sum+c*[.2126,.7152,.0722][i],0);}
 for(const selector of ['snd-source','input::placeholder']){const color=lastColor(selector);check(Boolean(color),'Explicit accessible text color '+selector);check(1.05/(luminance(color)+.05)>=4.5,'Minimum readable contrast on white '+selector);}

 console.log('PASS: '+total+' QA frontend regressions.');
}
run().catch(error=>{console.error(error);process.exit(1);});
