'use strict';
const assert=require('node:assert/strict'),fs=require('node:fs'),path=require('node:path'),{spawnSync}=require('node:child_process');
const {chromium}=require(process.env.SND_PLAYWRIGHT_PATH || 'playwright-core');
const base=process.env.SND_TEST_URL || 'http://127.0.0.1:8899',app=base+'/srbija-na-dlanu/';if(!['127.0.0.1','localhost'].includes(new URL(base).hostname))throw Error('Loopback only');
let n=0;const errors=[],password=require('node:crypto').randomBytes(20).toString('hex');
function check(v,label){assert.ok(v,label);n++;}
function fixture(op,data={}){const r=spawnSync(process.env.PHP_BIN || 'php',[path.join(__dirname,'http-fixture.php')],{input:JSON.stringify({op,...data}),encoding:'utf8',env:process.env});if(r.status)throw Error(r.stderr||r.stdout);return r.stdout.trim()?JSON.parse(r.stdout):null;}
async function stable(p){await p.waitForFunction(()=>document.querySelector('.snd-game')?.getAttribute('aria-busy')==='false' && document.querySelector('.snd-panel') && document.getElementById('snd-page-loader').hidden);}
async function shot(p,name){if(process.env.SND_SCREENSHOTS){fs.mkdirSync(process.env.SND_SCREENSHOTS,{recursive:true});await p.screenshot({path:path.join(process.env.SND_SCREENSHOTS,name+'.png'),fullPage:true,animations:'disabled'});}}
async function guess(p,name){await p.locator('.snd-guess-form input').fill(name);await p.locator('[role=option]').filter({hasText:name}).first().click();await p.locator('[data-cmd=guess]').click();await stable(p);}
async function scenario(browser,kind,target){const context=await browser.newContext({viewport:{width:1440,height:1050}}),p=await context.newPage();p.on('pageerror',e=>errors.push(e.message));const response=p.waitForResponse(r=>r.url().includes('/v1/start'));await p.goto(app+(kind==='district'?'okruzi/':'opstine/')+'?mode=practice&lang=sr&snd_theme=dark');const g=await(await response).json();await stable(p);fixture('scenario',{token:g.token,target});await p.reload();await stable(p);return{context,p,g};}
(async()=>{
 fixture('setup',{password});const browser=await chromium.launch({executablePath:process.env.CHROMIUM_BIN || '/usr/bin/chromium',headless:true,args:['--no-sandbox','--disable-dev-shm-usage']});
 try{
  const p=await browser.newPage({viewport:{width:2560,height:1440}});p.on('pageerror',e=>errors.push(e.message));
  for(const theme of ['dark','light']){
   await p.goto(app+'?lang=sr&snd_theme='+theme);
   check(await p.locator('body').evaluate((b,theme)=>getComputedStyle(b).backgroundColor===(theme==='dark'?'rgb(16, 42, 38)':'rgb(244, 247, 237)'),theme),'Forest palette background '+theme);
   check(await p.locator('.snd-card-action').first().evaluate(n=>getComputedStyle(n).boxShadow==='none' && getComputedStyle(n).filter==='none'),'Flat play action '+theme);
   for(const y of [0,800]){await p.evaluate(y=>scrollTo(0,y),y);check(await p.locator('.snd-river-background').evaluate(n=>{const b=n.getBoundingClientRect(),s=getComputedStyle(n);return b.x===0&&b.y===0&&b.width===innerWidth&&b.height===innerHeight&&s.maskImage.includes('rivers.svg')&&s.maskSize==='cover';}),'River layer covers wide viewport while scrolling '+theme+' '+y);}
   await shot(p,'sunrise-home-'+theme);
  }
  const borough=await scenario(browser,'municipality','m-bg-vracar');
  for(const name of ['Stari grad','Savski venac','Novi Beograd'])await guess(borough.p,name);
  check(await borough.p.locator('.snd-history .snd-direction').first().evaluate(n=>getComputedStyle(n).color==='rgb(255, 212, 71)'),'Sunrise direction arrows');
  await borough.p.locator('[data-cmd=giveup]').click();await stable(borough.p);
  for(const width of [1440,320,768]){
   await borough.p.setViewportSize({width,height:1000});await borough.p.waitForTimeout(120);
   const markers=await borough.p.locator('.snd-map-markers>g').evaluateAll(nodes=>nodes.map(n=>{const c=n.querySelector('circle').getBoundingClientRect(),s=document.querySelector('[data-map-region="'+(n.dataset.mapAttempt||n.dataset.mapAnswer)+'"]').getBoundingClientRect();return{size:c.width,number:n.querySelector('text')?.textContent,answer:n.hasAttribute('data-map-answer'),separate:c.right<s.left||c.left>s.right||c.bottom<s.top||c.top>s.bottom};}));
   check(markers.length===4 && markers.filter(m=>!m.answer).map(m=>m.number).join(',')==='1,2,3','Every municipality attempt remains numbered '+width);
   check(markers.every(m=>m.size<=(m.answer?16.2:12.2)),'Small consistent on-screen circles '+width);
   check(markers.filter(m=>!m.answer).every(m=>m.separate),'Small borough shapes stay visible beside callouts '+width);
   check(await borough.p.evaluate(()=>document.documentElement.scrollWidth<=innerWidth),'Municipality result fits '+width);await shot(borough.p,'sunrise-borough-markers-'+width);
  }
  await borough.context.close();
  const flag=await scenario(browser,'district','rs-02');await flag.p.locator('[data-cmd=giveup]').click();await stable(flag.p);await flag.p.locator('[data-cmd=next]').click();await stable(flag.p);
  for(let i=0;i<2;i++){await flag.p.locator('[data-cmd=skip]').click();await stable(flag.p);await flag.p.locator('[data-cmd=next]').click();await stable(flag.p);}
  check(await flag.p.locator('[data-zoom]').count()===4,'Flag round and all preview controls retained');
  await flag.p.locator('[data-choice]').first().click();await stable(flag.p);
  const state=await flag.p.locator('.snd-flag-card.is-correct').evaluate(n=>({bg:getComputedStyle(n).backgroundColor,border:getComputedStyle(n).borderColor,other:getComputedStyle(document.querySelector('.snd-flag-card:not(.is-correct)')).backgroundColor,explanation:getComputedStyle(document.querySelector('.snd-explanation')).backgroundColor}));
  check(state.border==='rgb(255, 212, 71)' && state.bg===state.other && state.bg===state.explanation,'Correct flag uses Sunrise outline and unchanged surface');
  await shot(flag.p,'sunrise-correct-flag');await flag.p.locator('[data-zoom]').first().click();await flag.p.locator('.snd-flag-preview').waitFor();await flag.p.keyboard.press('Escape');check(await flag.p.locator('[role=dialog]').count()===0,'Flag zoom still closes with Escape');await flag.context.close();
  const admin=await browser.newPage();await admin.goto(app+'prijava/?lang=sr');await admin.locator('input[name=login]').fill('snd-http-admin');await admin.locator('input[name=password]').fill(password);await admin.locator('.snd-auth-card button[type=submit]').click();await admin.waitForURL(/\/nalog\//);await admin.goto(base+'/wp-admin/admin.php?page=snd-settings');
  const fields=await admin.locator('input[type=color]').evaluateAll(nodes=>Object.fromEntries(nodes.map(n=>[n.name,n.value])));check(Object.keys(fields).length===9,'Seven base colors and two dark-theme overrides exposed in WP');
  await admin.locator('input[name="settings[max_guesses]"]').fill('8');await admin.locator('input[name="settings[points_step]"]').fill('7');await admin.locator('input[name="settings[highlight_color]"]').fill('#ffba08');await admin.locator('input[name="settings[ink_color]"]').fill('#101820');await admin.locator('input[name="settings[dark_surface_color]"]').fill('#243040');await admin.locator('input[name="settings[dark_button_color]"]').fill('#345123');await admin.locator('input[name="settings[surface_color]"]').fill('#fdfcf9');await admin.locator('input[name="settings[pale_color]"]').fill('#edf1df');
  await admin.locator('form').filter({has:admin.locator('input[name="settings[app_name]"]')}).locator('input[type=submit]').click();await admin.waitForURL(/saved=1/);
  await p.goto(app+'?lang=sr&snd_theme=dark');check(await p.locator('body').evaluate(n=>getComputedStyle(n).backgroundColor==='rgb(16, 24, 32)'),'Saved dark background reaches public page');check(await p.locator('.snd-card-action').first().evaluate(n=>getComputedStyle(n).backgroundColor==='rgb(52, 81, 35)' && getComputedStyle(n.querySelector('svg')).backgroundColor==='rgb(255, 186, 8)'),'Saved button and Sunrise override reach CTA');check(await p.locator('.snd-home-game').first().evaluate(n=>getComputedStyle(n).backgroundColor==='rgb(36, 48, 64)'),'Saved surface reaches cards');
  await p.goto(app+'?lang=sr&snd_theme=light');check(await p.locator('body').evaluate(n=>getComputedStyle(n).backgroundColor==='rgb(237, 241, 223)'),'Saved light background reaches public page');check(await p.locator('.snd-home-game').first().evaluate(n=>getComputedStyle(n).backgroundColor==='rgb(253, 252, 249)'),'Saved white role reaches cards');
  await admin.goto(base+'/wp-admin/admin.php?page=snd-settings');const invalid=await admin.request.post(base+'/wp-admin/admin-post.php',{form:{action:'snd_control_save',snd_palette_reset:'1',_wpnonce:'invalid'}});check(invalid.status()===403,'Palette reset rejects invalid nonce');
  await admin.locator('form').filter({has:admin.locator('input[name=snd_palette_reset]')}).locator('input[type=submit]').click();await admin.waitForURL(/saved=1/);
  check(await admin.locator('input[name="settings[primary_color]"]').inputValue()==='#2d6a4f' && await admin.locator('input[name="settings[ink_color]"]').inputValue()==='#102a26','Native reset restores suggested forest palette');
  check(await admin.locator('input[name="settings[max_guesses]"]').inputValue()==='8' && await admin.locator('input[name="settings[points_step]"]').inputValue()==='7' && await admin.locator('input[name="settings[registration_enabled]"]').isChecked(),'Palette reset preserves rules, scoring and registration');
  check(!errors.length,'No JavaScript exceptions: '+errors.join('; '));await admin.close();await p.close();console.log('PASS: '+n+' Sunrise palette, full-viewport background, adaptive borough callout and native WP color-save assertions.');
 }finally{await browser.close();fixture('cleanup');}
})().catch(e=>{console.error(e);process.exitCode=1;});
