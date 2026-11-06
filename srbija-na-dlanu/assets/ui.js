(function(){
  'use strict';
  const L=window.SND_L10N,cfg=L.config(),html=document.documentElement;
  const media=window.matchMedia?window.matchMedia('(prefers-color-scheme: dark)'):{matches:false};
  function apply(choice,remember){
    if(!['light','dark','auto'].includes(choice))choice=cfg.theme||'auto';
    if(!cfg.theme_switch)choice=cfg.theme||'auto';
    html.dataset.sndPreference=choice;html.dataset.sndTheme=choice==='auto'?(media.matches?'dark':'light'):choice;
    if(remember){try{localStorage.setItem('snd:theme',choice);}catch(_){}document.cookie='snd_theme='+choice+'; Max-Age=31536000; Path='+(cfg.cookie_path||'/')+'; SameSite=Lax'+(location.protocol==='https:'?'; Secure':'');}
    document.querySelectorAll('.snd-preferences [name=snd_theme]').forEach(select=>{select.value=choice;});
  }
  apply(html.dataset.sndPreference||cfg.theme||'auto',false);
  if(media.addEventListener)media.addEventListener('change',()=>{if(html.dataset.sndPreference==='auto')apply('auto',false);});
  document.querySelectorAll('.snd-preferences').forEach(form=>{
    const theme=form.querySelector('[name=snd_theme]'),language=form.querySelector('[name=lang]');
    if(theme)theme.addEventListener('change',()=>{apply(theme.value,true);send('preference',0);});
    if(language)language.addEventListener('change',()=>form.submit());
    const submit=form.querySelector('button');if(submit)submit.hidden=true;
  });
  let nonce='',identity=null,last=Date.now();
  async function session(){
    const target=document.querySelector('.snd-game[data-ajax]');let url=cfg.ajax || target&&target.dataset.ajax;
    if(!url){const origin=new URL(cfg.api);url=origin.origin+(new URL(cfg.cookie_path||'/',location.href).pathname)+'wp-admin/admin-ajax.php';}
    try{const u=new URL(url,location.href);u.searchParams.set('action','snd_session');const r=await fetch(u.href,{credentials:'same-origin',cache:'no-store'});const s=await r.json();nonce=s.nonce||'';identity=s.authenticated;}catch(_){identity=null;}
  }
  function send(event,seconds){
    if(!cfg.analytics||(!cfg.guest_analytics&&identity!==true)||identity===null||!cfg.view)return;
    const device=innerWidth<768?'mobile':innerWidth<1024?'tablet':'desktop';let referrer='';try{referrer=document.referrer?new URL(document.referrer).hostname:'';}catch(_){}
    const headers={'Content-Type':'application/json'};if(nonce)headers['X-WP-Nonce']=nonce;
    const url=new URL(cfg.api+'activity');url.searchParams.set('lang',cfg.language||'sr');
    fetch(url.href,{method:'POST',credentials:'same-origin',cache:'no-store',keepalive:true,headers,body:JSON.stringify({event,seconds,view:cfg.view,theme:html.dataset.sndTheme,device,referrer})}).catch(()=>{});
  }
  if(cfg.analytics)session().then(()=>send('pageview',0));
  setInterval(()=>{const now=Date.now(),seconds=Math.min(60,Math.round((now-last)/1000));last=now;if(document.visibilityState==='visible'&&document.hasFocus())send('heartbeat',seconds);},30000);
  document.addEventListener('visibilitychange',()=>{last=Date.now();});
})();
