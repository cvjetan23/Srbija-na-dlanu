'use strict';
// Real Chromium checks against an isolated WordPress; viewport sizes simulate
// mobile screens, not physical devices. No production users or credentials.
const assert=require('node:assert/strict'),fs=require('node:fs'),path=require('node:path');
const {chromium}=require(process.env.SND_PLAYWRIGHT_PATH || 'playwright-core');
const base=process.env.SND_TEST_URL || 'http://127.0.0.1:8899';
if(!['127.0.0.1','localhost'].includes(new URL(base).hostname))throw new Error('Refusing non-loopback site.');
const app=base+'/srbija-na-dlanu/',output=process.env.SND_SCREENSHOTS;
let total=0;const errors=[];
function check(v,label){assert.ok(v,label);total++;}
async function navigate(page,url){await page.goto(url,{waitUntil:'domcontentloaded'});await page.locator('.snd-preferences').waitFor();}
async function capture(page,name){if(output){fs.mkdirSync(output,{recursive:true});await page.screenshot({path:path.join(output,name+'.png'),animations:'disabled'});}}
async function clickRegion(page,selector){
 const region=page.locator(selector);await region.scrollIntoViewIfNeeded();
 const point=await region.locator('path').evaluate(shape=>{
   const b=shape.getBBox(),points=[];
   for(let row=1;row<20;row++)for(let col=1;col<20;col++){
     const p=new DOMPoint(b.x+b.width*col/20,b.y+b.height*row/20);
     if(shape.isPointInFill(p))points.push({p,dist:Math.hypot(col-10,row-10)});
   }
   points.sort((a,b)=>a.dist-b.dist);if(!points.length)return null;
   const p=points[0].p.matrixTransform(shape.getScreenCTM());return {x:p.x,y:p.y};
 });check(point,'SVG has clickable interior');
 const target=await region.getAttribute('href');await Promise.all([page.waitForURL(target),page.mouse.click(point.x,point.y)]);
 await page.locator('.snd-preferences').waitFor();
}
(async()=>{
 const browser=await chromium.launch({executablePath:process.env.CHROMIUM_BIN || '/usr/bin/chromium',headless:true,args:['--no-sandbox','--disable-dev-shm-usage']});
 try{
  const context=await browser.newContext({viewport:{width:1440,height:1050}});const page=await context.newPage();page.on('pageerror',e=>errors.push(e.message));
  for(const lang of ['sr','en','de','ru']){
   await navigate(page,app+'baza-znanja/?lang='+lang);check(await page.locator('.snd-map-region').count()===30,'30 district links '+lang);check(await page.locator('.snd-district-card').count()===30,'30 district cards '+lang);
   const order=await page.evaluate(()=>document.querySelector('.snd-map-explorer').compareDocumentPosition(document.querySelector('.snd-district-grid')) & Node.DOCUMENT_POSITION_FOLLOWING);check(order,'Map is before district list');
   await page.locator('.snd-language-picker summary').click();check(await page.locator('.snd-language-options').isVisible(),'Obvious language selector opens');await page.keyboard.press('Escape');check((await page.locator('.snd-language-picker').getAttribute('open'))===null,'Escape closes selector');
   const district=page.locator('.snd-map-region[data-territory="rs-12"]');await district.focus();check((await page.locator('[data-map-name]').innerText())===(await district.getAttribute('data-name')),'Keyboard focus names district');
   await clickRegion(page,'.snd-map-region[data-territory="rs-12"]');check(new URL(page.url()).searchParams.get('okrug')==='rs-12','Country map opens district');check(await page.locator('.snd-map-region').count()===7,'District contains seven municipalities');
   const first=page.locator('.snd-map-region').first(),id=await first.getAttribute('data-territory'),name=await first.getAttribute('data-name');
   await clickRegion(page,'.snd-map-region[data-territory="'+id+'"]');check(new URL(page.url()).searchParams.get('opstina')===id,'District map opens municipality');check((await page.locator('h1').innerText())===name,'Municipality detail name');check(await page.locator('.snd-map-region.is-selected').getAttribute('data-territory')===id,'Selected municipality highlighted');check(await page.locator('.snd-detail-shape path').count()===1,'Municipality outline shown');
   await page.locator('.snd-language-picker summary').click();await page.locator('a[data-language="de"]').click();check(new URL(page.url()).searchParams.get('opstina')===id && new URL(page.url()).searchParams.get('lang')==='de','Language switch keeps municipality');
  }
  await navigate(page,app+'baza-znanja/?lang=sr');await page.locator('[data-snd-theme-choice=dark]').click();check(await page.locator('html').getAttribute('data-snd-theme')==='dark','Moon selects dark');check(await page.locator('[data-snd-theme-choice=dark]').getAttribute('aria-pressed')==='true','Dark selected state');check(await page.evaluate(()=>localStorage.getItem('snd:theme'))==='dark','Theme stored');await capture(page,'desktop-dark');
  await page.reload({waitUntil:'domcontentloaded'});check(await page.locator('html').getAttribute('data-snd-theme')==='dark','Theme survives reload');await page.locator('[data-snd-theme-choice=light]').click();check(await page.locator('html').getAttribute('data-snd-theme')==='light','Sun selects light');await capture(page,'desktop-light');
  await navigate(page,app+'baza-znanja/?lang=sr&snd_theme=dark');await page.locator('[data-snd-theme-choice=light]').click();check(!new URL(page.url()).searchParams.has('snd_theme'),'Explicit theme query cleared after user changes choice');await page.reload({waitUntil:'domcontentloaded'});check(await page.locator('html').getAttribute('data-snd-theme')==='light','Changed choice survives explicit-theme URL reload');
  await page.emulateMedia({colorScheme:'dark'});await page.locator('[data-snd-theme-choice=auto]').click();check(await page.locator('html').getAttribute('data-snd-theme')==='dark','Auto follows dark device');await page.emulateMedia({colorScheme:'light'});await page.waitForFunction(()=>document.documentElement.dataset.sndTheme==='light');check(true,'Auto follows live device change');
  await page.locator('.snd-language-picker summary').click();await page.locator('h1').click();check((await page.locator('.snd-language-picker').getAttribute('open'))===null,'Outside click closes picker');
  for(const width of [320,390,768]){
   const mobile=await browser.newContext({viewport:{width,height:844},isMobile:width<768,hasTouch:true});const p=await mobile.newPage();p.on('pageerror',e=>errors.push(e.message));await navigate(p,app+'baza-znanja/?lang=ru');
   const dimensions=await p.evaluate(()=>({content:document.documentElement.scrollWidth,viewport:innerWidth}));check(dimensions.content<=width && dimensions.viewport===width,'No horizontal overflow '+width);
   await p.locator('.snd-language-picker summary').tap();check(await p.locator('.snd-language-options').isVisible(),'Touch opens language picker '+width);const bounds=await p.locator('.snd-language-options').boundingBox();check(bounds.x>=0 && bounds.x+bounds.width<=width,'Picker stays in viewport '+width);await capture(p,'languages-'+width);
   await Promise.all([p.waitForURL(u=>u.searchParams.get('lang')==='sr',{waitUntil:'domcontentloaded'}),p.locator('a[data-language=sr]').tap()]);check(new URL(p.url()).searchParams.get('lang')==='sr','Touch changes language '+width);await p.locator('[data-snd-theme-choice=dark]').tap();check(await p.locator('html').getAttribute('data-snd-theme')==='dark','Touch selects moon '+width);
   await clickRegion(p,'.snd-map-region[data-territory="rs-25"]');check(new URL(p.url()).searchParams.get('okrug')==='rs-25','KiM district opens on small screen');check(await p.locator('.snd-member-list a').count()>0,'KiM municipalities retained');await capture(p,'district-'+width);await mobile.close();
  }
  const nojs=await browser.newContext({javaScriptEnabled:false,viewport:{width:1280,height:900}});const p=await nojs.newPage();await p.goto(app+'baza-znanja/?lang=sr');check(await p.locator('.snd-map-region').count()===30,'Map works without JS');await p.locator('.snd-theme-options button[value=dark]').click();check(await p.locator('html').getAttribute('data-snd-theme')==='dark','Native form selects dark without JS');await p.locator('.snd-language-picker summary').click();await p.locator('a[data-language=en]').click();check(await p.locator('html').getAttribute('lang')==='en','Native language link without JS');await nojs.close();
  check(errors.length===0,'No browser JavaScript errors: '+errors.join('; '));console.log('PASS: '+total+' real Chromium atlas, language, theme, keyboard and responsive assertions.');
 }finally{await browser.close();}
})().catch(error=>{console.error(error);process.exitCode=1;});
