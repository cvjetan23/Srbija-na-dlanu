'use strict';
const assert=require('node:assert/strict');
global.document={querySelectorAll:()=>[],documentElement:{dataset:{}}};global.location={href:'https://example.test/srbija-na-dlanu/okruzi/'};global.localStorage={getItem:()=>null,setItem(){}};
const {Game}=require('../srbija-na-dlanu/assets/game.js');let count=0;
const check=(v,label)=>{assert.ok(v,label);count++;};
function game(){const g=Object.create(Game.prototype);g.api='https://example.test/wp-json/srbija-na-dlanu/v1/';g.root={dataset:{ajax:'https://example.test/wp-admin/admin-ajax.php'}};g.account={authenticated:true,nonce:'valid-nonce'};return g;}
const response=(data,status=200)=>({ok:status<400,json:async()=>data});
(async()=>{
 const g=game(),calls=[];global.fetch=async(url,o)=>{calls.push({url,o});if(url.includes('/wp-json/'))throw new TypeError('Failed to fetch');return response({token:'opaque',state:{revision:0}});};
 await g.request('start',{kind:'district',mode:'practice',fresh:true});check(calls.length===2,'Network error uses one fallback');const first=JSON.parse(calls[0].o.body),fields=new URLSearchParams(calls[1].o.body);check(fields.get('action')==='snd_game'&&fields.get('route')==='start','WP AJAX bridge selected');check(fields.get('_wpnonce')==='valid-nonce','Account nonce forwarded');check(JSON.parse(fields.get('payload')).request_id===first.request_id&&first.request_id.length===32,'Same idempotency ID on both transports');
 await g.request('resume',{token:'opaque'});check(calls.length===3&&calls[2].url.includes('admin-ajax.php'),'Further requests keep healthy transport');
 const auth=game();let requests=0;global.fetch=async()=>{requests++;return response({code:'rest_cookie_invalid_nonce',message:'expired'},403);};await assert.rejects(auth.request('action',{}),e=>e.code==='rest_cookie_invalid_nonce');check(requests===1,'Authentication failures are not retried through another route');
 const committed=game();committed.state={revision:0};committed.token='opaque';committed.setBusy=()=>{};committed.render=()=>{};committed.focusPanel=()=>{};committed.accept=function(r){this.state=r.state;};
 global.fetch=async(url,o)=>{if(url.includes('/wp-json/'))throw new TypeError('HTTP/2 stream closed');const f=new URLSearchParams(o.body);if(f.get('route')==='action')return response({code:'snd_stale',message:'stale'},409);return response({state:{revision:1}});};
 await committed.action('giveup');check(committed.state.revision===1&&committed.error==='','Lost committed response resumes without false failure or duplicate action');
 const unavailable=game();global.fetch=async()=>{throw new TypeError('Failed to fetch');};await assert.rejects(unavailable.request('resume',{token:'opaque'}),e=>e.networkFailure&&!e.message.includes('Failed to fetch'));check(true,'Friendly network failure when both paths fail');
 console.log('PASS: '+count+' transport recovery and lost-response assertions.');
})().catch(e=>{console.error(e);process.exitCode=1;});
