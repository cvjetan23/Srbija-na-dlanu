'use strict';
// Verify the actual responsive header, not a duplicate of its CSS rules.
const assert=require('node:assert/strict'),fs=require('node:fs'),path=require('node:path');
const {chromium}=require(process.env.SND_PLAYWRIGHT_PATH || 'playwright-core');
const base=process.env.SND_TEST_URL || 'http://127.0.0.1:8899';
if(!['127.0.0.1','localhost'].includes(new URL(base).hostname))throw Error('Loopback only');
const app=base+'/srbija-na-dlanu/';let checks=0;const errors=[];
function check(ok,label){assert.ok(ok,label);checks++;}
(async()=>{
 const browser=await chromium.launch({executablePath:process.env.CHROMIUM_BIN || '/usr/bin/chromium',headless:true,args:['--no-sandbox','--disable-dev-shm-usage']});
 try{
  const context=await browser.newContext({viewport:{width:640,height:900}}),p=await context.newPage();p.on('pageerror',e=>errors.push(e.message));
  for(const width of [320,640,1024,1440]){
   await p.setViewportSize({width,height:900});
   for(const lang of ['sr','en','de','ru'])for(const theme of ['light','dark']){
    await p.goto(app+'?lang='+lang+'&snd_theme='+theme,{waitUntil:'domcontentloaded'});await p.locator('.snd-app-header').waitFor();if(width<=960){check(await p.locator('.snd-app-header').evaluate(n=>n.getBoundingClientRect().height<=64),'Collapsed mobile header stays compact '+[width,lang,theme]);check(await p.locator('.snd-preferences').isHidden(),'Tools are inside mobile menu '+[width,lang,theme]);const contentY=await p.locator('#snd-content').evaluate(n=>n.getBoundingClientRect().top);await p.locator('[data-snd-menu-toggle]').click();check(await p.locator('.snd-app-header').evaluate(n=>n.getBoundingClientRect().height<=64)&&Math.abs((await p.locator('#snd-content').evaluate(n=>n.getBoundingClientRect().top))-contentY)<1,'Opening the overlay leaves the page and header in place '+[width,lang,theme]);}else{check(await p.locator('.snd-app-header').evaluate(n=>n.getBoundingClientRect().height<=64),'Compact desktop header '+[width,lang,theme]);}
    const b=await p.evaluate(()=>{const rect=s=>{const r=document.querySelector(s).getBoundingClientRect();return {x:r.x,y:r.y,right:r.right,bottom:r.bottom,width:r.width,height:r.height};};return {language:rect('.snd-language-picker'),themes:rect('.snd-theme-options'),preferences:rect('.snd-preferences'),brand:rect('.snd-app-brand'),account:rect('.snd-account-link'),nav:rect('.snd-app-nav'),first:rect('.snd-app-nav a'),icon:rect('.snd-theme-options svg'),overflow:document.documentElement.scrollWidth>innerWidth};});
    check(!b.overflow,'Header stays inside viewport '+[width,lang,theme]);
    check(b.themes.height<=40 && b.icon.width<=18,'Compact display controls '+[width,lang,theme]);
    check(b.language.right+8<=b.themes.x,'Language and theme never overlap '+[width,lang,theme]);
    if(width<=960){
     check(Math.abs(b.language.x-b.preferences.x)<1 && Math.abs(b.themes.right-b.preferences.right)<1,'Language at left and themes at right '+[width,lang,theme]);
     check(Math.abs(b.first.x-b.brand.x)<1 && b.preferences.y>=b.nav.bottom,'Menu starts at left above tools '+[width,lang,theme]);
     check(b.account.x>=b.brand.right && b.account.bottom<=b.nav.y,'Account stays on the compact top row '+[width,lang,theme]);
     const contentY=await p.locator('#snd-content').evaluate(n=>n.getBoundingClientRect().top);await p.locator('.snd-games-menu summary').click();check(Math.abs((await p.locator('#snd-content').evaluate(n=>n.getBoundingClientRect().top))-contentY)<1&&await p.locator('.snd-app-header').evaluate(n=>n.getBoundingClientRect().height<=64),'Expanding games never moves the page '+[width,lang,theme]);await p.locator('.snd-games-menu summary').click();
    }
    if(width===640 && lang==='sr' && theme==='dark' && process.env.SND_SCREENSHOTS){fs.mkdirSync(process.env.SND_SCREENSHOTS,{recursive:true});await p.screenshot({path:path.join(process.env.SND_SCREENSHOTS,'compact-header-640.png'),animations:'disabled'});}
   }
  }
  await p.setViewportSize({width:390,height:460});await p.goto(app+'?lang=sr&snd_theme=light');const shortY=await p.locator('#snd-content').evaluate(n=>n.getBoundingClientRect().top);await p.locator('[data-snd-menu-toggle]').click();await p.locator('.snd-games-menu summary').click();
  check(Math.abs((await p.locator('#snd-content').evaluate(n=>n.getBoundingClientRect().top))-shortY)<1,'Expanded overlay preserves content on a short screen');
  check(await p.locator('.snd-header-panel').evaluate(n=>{const r=n.getBoundingClientRect();return r.top>=0&&r.bottom<=innerHeight-8&&n.scrollHeight>n.clientHeight;}),'Long menu stays within the screen and scrolls internally');
  await p.locator('[data-snd-theme-choice=auto]').scrollIntoViewIfNeeded();check(await p.locator('[data-snd-theme-choice=auto]').evaluate(n=>{const r=n.getBoundingClientRect(),p=n.closest('.snd-header-panel').getBoundingClientRect();return r.top>=p.top&&r.bottom<=p.bottom;}),'Display controls remain reachable in the scrolling overlay');await p.keyboard.press('Escape');
  await p.setViewportSize({width:320,height:800});await p.goto(app+'baza-znanja/?okrug=rs-00&lang=sr&snd_theme=dark',{waitUntil:'domcontentloaded'});
  await p.locator('[data-snd-menu-toggle]').click();const picker=p.locator('.snd-language-picker summary');await picker.focus();await p.keyboard.press('Enter');check(await p.locator('.snd-language-options').isVisible(),'Keyboard opens language picker');
  const bounds=await p.locator('.snd-language-options').boundingBox();check(bounds.x>=0 && bounds.x+bounds.width<=320,'Open language menu fits mobile viewport');
  await Promise.all([p.waitForURL(u=>u.searchParams.get('lang')==='de',{waitUntil:'domcontentloaded'}),p.locator('[data-language=de]').click()]);check(new URL(p.url()).searchParams.get('okrug')==='rs-00','Language change preserves current district');
  await p.locator('[data-snd-menu-toggle]').click();await p.locator('[data-snd-theme-choice=light]').click();check(await p.locator('html').getAttribute('data-snd-theme')==='light','Small theme control changes theme');
  const plain=await browser.newContext({javaScriptEnabled:false,viewport:{width:640,height:900}}),np=await plain.newPage();await np.goto(app+'?lang=sr&snd_theme=dark',{waitUntil:'domcontentloaded'});await np.locator('.snd-language-picker summary').click();await Promise.all([np.waitForURL(u=>u.searchParams.get('lang')==='en',{waitUntil:'domcontentloaded'}),np.locator('[data-language=en]').click()]);check(new URL(np.url()).searchParams.get('lang')==='en','Language selector works without JS');await Promise.all([np.waitForURL(u=>u.searchParams.get('snd_theme')==='light',{waitUntil:'domcontentloaded'}),np.locator('[data-snd-theme-choice=light]').click()]);check(await np.locator('html').getAttribute('data-snd-theme')==='light','Native theme form works without JS');await plain.close();
  check(!errors.length,'No JavaScript errors');console.log('PASS: '+checks+' compact header assertions; four widths, four languages, both themes and native controls.');
 }finally{await browser.close();}
})().catch(e=>{console.error(e);process.exitCode=1;});
