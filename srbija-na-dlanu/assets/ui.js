(function(){
  'use strict';
  const L=window.SND_L10N,cfg=L.config(),html=document.documentElement;
  const media=window.matchMedia?window.matchMedia('(prefers-color-scheme: dark)'):{matches:false};
  function apply(choice,remember){
    if(!['light','dark','auto'].includes(choice))choice=cfg.theme||'auto';
    if(!cfg.theme_switch)choice=cfg.theme||'auto';
    if(remember){html.classList.add('snd-theme-changing');setTimeout(()=>html.classList.remove('snd-theme-changing'),350);}
    html.dataset.sndPreference=choice;html.dataset.sndTheme=choice==='auto'?(media.matches?'dark':'light'):choice;
    if(remember){try{localStorage.setItem('snd:theme',choice);}catch(_){}document.cookie='snd_theme='+choice+'; Max-Age=31536000; Path='+(cfg.cookie_path||'/')+'; SameSite=Lax'+(location.protocol==='https:'?'; Secure':'');
      if(window.history && window.history.replaceState){const url=new URL(location.href);if(url.searchParams.has('snd_theme')){url.searchParams.delete('snd_theme');window.history.replaceState(null,'',url.href);}}
    }
    document.querySelectorAll('[data-snd-theme-choice]').forEach(button=>{button.setAttribute('aria-pressed',String(button.dataset.sndThemeChoice===choice));});
  }
  apply(html.dataset.sndPreference||cfg.theme||'auto',false);
  if(media.addEventListener)media.addEventListener('change',()=>{if(html.dataset.sndPreference==='auto')apply('auto',false);});
  document.querySelectorAll('[data-snd-theme-choice]').forEach(button=>{
    button.addEventListener('click',event=>{event.preventDefault();apply(button.dataset.sndThemeChoice,true);send('preference',0);});
  });
  document.querySelectorAll('.snd-language-picker').forEach(picker=>{
    picker.addEventListener('keydown',event=>{if(event.key==='Escape'){picker.open=false;picker.querySelector('summary').focus();}});
    document.addEventListener('click',event=>{if(!picker.contains(event.target))picker.open=false;});
  });
  const pageLoader=document.getElementById('snd-page-loader');let loadTimer,clearTimer;
  function loading(on){if(!pageLoader)return;clearTimeout(loadTimer);clearTimeout(clearTimer);if(!on){pageLoader.hidden=true;return;}loadTimer=setTimeout(()=>{pageLoader.hidden=false;},90);clearTimer=setTimeout(()=>{pageLoader.hidden=true;},12000);}
  document.addEventListener('click',event=>{
    if(event.defaultPrevented || event.button>0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || !event.target.closest)return;
    const a=event.target.closest('.snd-app a[href]');if(!a || a.target==='_blank' || a.hasAttribute('download'))return;
    const url=new URL(a.getAttribute('href'),location.href);if(url.origin!==location.origin || (url.pathname===location.pathname && url.search===location.search && url.hash))return;
    loading(true);
  });
  document.addEventListener('submit',event=>{if(!event.defaultPrevented && event.target.closest && event.target.closest('.snd-app'))loading(true);});
  window.addEventListener('pageshow',()=>loading(false));
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
