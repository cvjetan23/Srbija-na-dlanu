'use strict';
const assert=require('node:assert/strict'),fs=require('node:fs'),path=require('node:path'),{spawnSync}=require('node:child_process');
const {chromium}=require(process.env.SND_PLAYWRIGHT_PATH || 'playwright-core');
const base=process.env.SND_TEST_URL || 'http://127.0.0.1:8899',app=base+'/srbija-na-dlanu/';
if(!['127.0.0.1','localhost'].includes(new URL(base).hostname))throw new Error('Refusing non-loopback site.');
let count=0;const check=(v,label)=>{assert.ok(v,label);count++;};
const fixture=(op,data={})=>{const r=spawnSync(process.env.PHP_BIN || 'php',[path.join(__dirname,'http-fixture.php')],{input:JSON.stringify({op,...data}),encoding:'utf8',env:process.env});if(r.status)throw new Error(r.stderr || r.stdout);return r.stdout.trim()?JSON.parse(r.stdout):null;};
const password=require('node:crypto').randomBytes(20).toString('hex');
async function screenshot(p,name){if(process.env.SND_SCREENSHOTS){fs.mkdirSync(process.env.SND_SCREENSHOTS,{recursive:true});await p.screenshot({path:path.join(process.env.SND_SCREENSHOTS,name+'.png'),animations:'disabled',fullPage:true});}}
(async()=>{
 fixture('setup',{password});const browser=await chromium.launch({executablePath:process.env.CHROMIUM_BIN || '/usr/bin/chromium',headless:true,args:['--no-sandbox','--disable-dev-shm-usage']});
 try{
  const p=await browser.newPage({viewport:{width:1280,height:900}});const errors=[];p.on('pageerror',e=>errors.push(e.message));
  await p.goto(app+'baza-znanja/?okrug=rs-28&lang=sr',{waitUntil:'domcontentloaded'});
  check(await p.locator('.snd-member-table tbody tr').count()===6,'KiM municipalities shown as six table rows');check(await p.locator('.snd-member-table thead th').count()===3,'Name population and census year columns');check((await p.locator('.snd-member-table tbody td').allTextContents()).every(s=>!s.includes('2022')),'No invented census values for KiM');
  check(await p.locator('.snd-preference-label').count()===0,'Language and theme captions removed');const style=await p.locator('.snd-language-picker summary').evaluate(n=>{const s=getComputedStyle(n);return {border:s.borderTopWidth,bg:s.backgroundColor,radius:s.borderRadius};});check(style.border==='0px'&&style.bg==='rgba(0, 0, 0, 0)'&&style.radius==='0px','Flat language menu item');await screenshot(p,'municipality-table-kim');
  await p.goto(app+'baza-znanja/?okrug=rs-12&lang=en',{waitUntil:'domcontentloaded'});check(await p.locator('.snd-member-table tbody tr').count()===7,'Seven populated municipalities');check((await p.locator('.snd-member-table tbody td').allTextContents()).includes('2022'),'Known census year displayed');await screenshot(p,'municipality-table-census');
  await p.setViewportSize({width:320,height:844});check(await p.evaluate(()=>document.documentElement.scrollWidth<=innerWidth),'Table fits narrow screen');await screenshot(p,'compact-menu-mobile');
  const reduced=await browser.newContext({reducedMotion:'reduce'});const rp=await reduced.newPage();await rp.goto(app+'okruzi/',{waitUntil:'domcontentloaded'});check(await rp.locator('.snd-compass-loader .snd-loader-needle').first().evaluate(n=>getComputedStyle(n).animationName)==='none','Reduced motion disables compass animation');await reduced.close();
  await p.goto(app+'prijava/?lang=sr',{waitUntil:'domcontentloaded'});await p.locator('#snd-login').fill('snd-http-a');await p.locator('#snd-password').fill(password);await p.locator('.snd-auth-card button[type=submit]').click();try{await p.waitForURL(/\/nalog\//,{timeout:8000,waitUntil:'domcontentloaded'});}catch(e){throw new Error('Native sign in failed: '+await p.locator('.snd-auth-error').textContent().catch(()=>p.url()));}
  let releaseBoot;const bootGate=new Promise(r=>releaseBoot=r);await p.route('**/wp-json/srbija-na-dlanu/v1/bootstrap*',async r=>{await bootGate;await r.continue();});
  await p.goto(app+'opstine/?mode=practice&lang=sr',{waitUntil:'domcontentloaded'});check(await p.locator('.snd-loading .snd-compass-loader').isVisible(),'Compass loader visible while game waits');releaseBoot();await p.locator('.snd-board').waitFor();
  let releaseResume;const resumeGate=new Promise(r=>releaseResume=r);let fallbackActions=0,actualActions=0;
  await p.route('**/wp-json/srbija-na-dlanu/v1/action*',async r=>{actualActions++;await r.fetch();await r.abort('failed');});
  await p.route('**/wp-admin/admin-ajax.php*',async r=>{const f=new URLSearchParams(r.request().postData() || '');if(f.get('action')==='snd_game'&&f.get('route')==='action')fallbackActions++;if(f.get('route')==='resume')await resumeGate;await r.continue();});
  await p.locator('[data-cmd=giveup]').click();await p.waitForRequest(r=>r.url().includes('admin-ajax.php')&&(r.postData() || '').includes('route=resume'));check(await p.locator('#snd-page-loader').isHidden(),'Lost-response recovery keeps the current game visible without blur');releaseResume();await p.locator('[data-cmd=finish]').waitFor();
  check(actualActions===1&&fallbackActions===1,'One REST write and one revision-guarded fallback');check(await p.locator('.snd-error').isHidden(),'Committed action recovered without Failed to fetch banner');
  await p.locator('[data-cmd=finish]').click();await p.locator('[data-cmd=share]').waitFor();const session=await (await p.request.get(base+'/wp-admin/admin-ajax.php?action=snd_session')).json();check(session.progress.municipality.practice_count===1,'Real logged-in progress recorded exactly once after lost response');
  check(errors.length===0,'No JavaScript exceptions: '+errors.join('; '));console.log('PASS: '+count+' table, loader, reduced-motion and authenticated lost-response browser assertions.');
 }finally{await browser.close();fixture('cleanup');}
})().catch(e=>{console.error(e);process.exitCode=1;});
