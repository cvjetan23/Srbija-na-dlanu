(function () {
  'use strict';
  const L = typeof module === 'object' && module.exports ? require('./i18n.js') : window.SND_L10N;
  const esc = value => String(value == null ? '' : value).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  const cyr = { а:'a',б:'b',в:'v',г:'g',д:'d',ђ:'dj',е:'e',ж:'z',з:'z',и:'i',ј:'j',к:'k',л:'l',љ:'lj',м:'m',н:'n',њ:'nj',о:'o',п:'p',р:'r',с:'s',т:'t',ћ:'c',у:'u',ф:'f',х:'h',ц:'c',ч:'c',џ:'dz',ш:'s' };
  const normalize = value => String(value).toLowerCase().replace(/[а-яђјљњћџ]/g, c => cyr[c] || c).replace(/đ/g, 'dj').normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/[^a-z0-9 ]/g, ' ').replace(/\s+/g, ' ').trim();
  const storage = {
    get(key, fallback = null) { try { const value = localStorage.getItem('snd:v1:' + key); const parsed = value ? JSON.parse(value) : fallback; if (parsed === null || (fallback && typeof fallback === 'object' && (typeof parsed !== 'object' || Array.isArray(parsed) !== Array.isArray(fallback)))) return fallback; return parsed; } catch (_) { return fallback; } },
    set(key, value) { try { localStorage.setItem('snd:v1:' + key, JSON.stringify(value)); return true; } catch (_) { return false; } },
    remove(key) { try { localStorage.removeItem('snd:v1:' + key); } catch (_) {} }
  };
  const safeUrl = value => { try { const u = new URL(value, location.href); return ['http:', 'https:'].includes(u.protocol) ? esc(u.href) : ''; } catch (_) { return ''; } };
  const sourceLink = (url, label = 'Izvor podatka ↗') => url ? `<a class="snd-source" href="${safeUrl(url)}" target="_blank" rel="noopener noreferrer"><span>${esc(L.text(label).replace(/\s*↗$/,''))}</span>${icon('external')}</a>` : '';
  const licenseUrl = label => {
    const match = /^CC BY(-SA)? (\d+\.\d+)$/.exec(label || '');
    if (match) return `https://creativecommons.org/licenses/by${match[1] ? '-sa' : ''}/${match[2]}/`;
    if (label === 'CC0') return 'https://creativecommons.org/publicdomain/zero/1.0/';
    if (['Public domain','PDM','PD'].includes(label)) return 'https://creativecommons.org/publicdomain/mark/1.0/';
    return '';
  };


  const formatDays = n => L.days(n);
  const directionIcon = arrow => {
    const index=['↑','↗','→','↘','↓','↙','←','↖'].indexOf(arrow),angle=Math.max(0,index)*45;
    return `<svg class="snd-direction-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20V4m-6 6 6-6 6 6" transform="rotate(${angle} 12 12)"/></svg>`;
  };
  const icon = name => {
    const paths={external:'<path d="M7 17 17 7M7 7h10v10"/>',enter:'<path d="M20 5v6a3 3 0 0 1-3 3H4m5-5-5 5 5 5"/>',help:'<circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.5 2.5 0 1 1 4 2c-1 .6-1.5 1.2-1.5 2.5M12 17h.01"/>',hint:'<path d="M9 18h6m-5 3h4M8.5 14.5a6 6 0 1 1 7 0L15 17H9l-.5-2.5Z"/>',eye:'<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>',next:'<path d="M4 12h16m-6-6 6 6-6 6"/>',finish:'<path d="M5 21V4m0 0c5-4 9 4 14 0v10c-5 4-9-4-14 0"/>',skip:'<path d="m5 4 12 8-12 8V4Zm14 0v16"/>',share:'<circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.6 10.5 6.8-4m-6.8 7 6.8 4"/>',refresh:'<path d="M20 7v5h-5M4 17v-5h5m10.5-1a7.5 7.5 0 0 0-13-5M4.5 13a7.5 7.5 0 0 0 13 5"/>',zoom:'<circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 5 5M7 10.5h7m-3.5-3.5v7"/>',correct:'<path d="m5 12 4 4L19 6"/>',wrong:'<path d="m6 6 12 12M18 6 6 18"/>',sources:'<path d="M12 5v16m0-16c-3-3-7-3-10-1v15c3-2 7-2 10 2 3-4 7-4 10-2V4c-3-2-7-2-10 1Z"/>'};
    return `<svg class="snd-action-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">${paths[name] || paths.next}</svg>`;
  };
  const nextSuggestionIndex = (active,count,key) => !count ? -1 : active < 0 ? key === 'ArrowUp' ? count - 1 : 0 : (active + (key === 'ArrowDown' ? 1 : -1) + count) % count;
  const touchMoved = (start,event) => Boolean(start && Math.hypot(event.clientX-start.x,event.clientY-start.y)>10);
  const kindStats = (all,kind) => { const value=all && typeof all[kind]==='object' && all[kind] || {}; return {...value,days:Array.isArray(value.days)?value.days:[],discovered:Array.isArray(value.discovered)?value.discovered:[],streak:Number(value.streak)||0}; };
  const outcome = s => L.text(s.solved ? 'Pogođeno' : s.guesses.length >= s.max_guesses ? s.max_guesses===6 ? 'Nije pogođeno za šest pokušaja' : L.t('Nije pogođeno za {count} pokušaja',{count:s.max_guesses}) : 'Odgovor je otkriven');
  const questionSources = q => Array.isArray(q.sources) && q.sources.length ? q.sources.map(x=>sourceLink(x.url,x.label)).join('<br>') : sourceLink(q.source);

  class Game {
    constructor(root) {
      this.root = root; this.api = root.dataset.api; this.assets = root.dataset.assets;
      const preferences = storage.get('preferences', {});
      const params = new URL(location.href).searchParams;
      this.fixedKind = ['district','municipality'].includes(root.dataset.kind) ? root.dataset.kind : '';
      this.kind = this.fixedKind || (params.get('kind') === 'municipality' ? 'municipality' : params.get('kind') === 'district' ? 'district' : preferences.kind === 'municipality' ? 'municipality' : 'district');
      this.account = { authenticated:false, progress:null, active:{} }; this.accountChecked = false;
      this.mode = ['daily','practice'].includes(params.get('mode')) ? params.get('mode') : preferences.mode === 'practice' ? 'practice' : 'daily';
      this.linkDate = /^\d{4}-\d{2}-\d{2}$/.test(params.get('date') || '') ? params.get('date') : '';
      this.recovery = null; this.notice = ''; this.mediaFailed = false; this.mediaAttempt = 0;
      this.state = null; this.token = null; this.busy = false; this.requestNumber = 0;
      this.selected = null; this.error = ''; this.ready = this.boot();
    }
    async request(route, body) {
      if(route==='start' && body && !body.request_id){
        const bytes=new Uint8Array(16);if(typeof crypto!=='undefined' && crypto.getRandomValues)crypto.getRandomValues(bytes);else for(let i=0;i<bytes.length;i++)bytes[i]=Math.floor(Math.random()*256);
        body={...body,request_id:[...bytes].map(n=>n.toString(16).padStart(2,'0')).join('')};
      }
      const perform=async ajax=>{
        const controller=new AbortController(),timer=setTimeout(()=>controller.abort(),20000);
        try{
          const headers=body?{'Content-Type':'application/json'}:{};
          if(this.account.authenticated)headers['X-WP-Nonce']=this.account.nonce;
          if(document.documentElement && document.documentElement.dataset.sndTheme)headers['X-SND-Theme']=document.documentElement.dataset.sndTheme;
          const lang=L.config().language || this.root.dataset.lang || 'sr';let address=this.api+route,payload=body?JSON.stringify(body):undefined;
          if(ajax){
            address=this.root.dataset.ajax;const fields=new URLSearchParams({action:'snd_game',route,payload:JSON.stringify(body || {})});
            if(this.account.authenticated)fields.set('_wpnonce',this.account.nonce);
            payload=fields.toString();headers['Content-Type']='application/x-www-form-urlencoded;charset=UTF-8';
          }
          if(lang!=='sr')address+=(address.includes('?')?'&':'?')+'lang='+encodeURIComponent(lang);
          const response=await fetch(address,{method:ajax || body?'POST':'GET',credentials:'same-origin',cache:'no-store',headers,body:payload,signal:controller.signal});
          let data;try{data=await response.json();}catch(_){const e=new Error('Igra nije mogla da učita odgovor. Osveži stranicu ili pokušaj ponovo za nekoliko trenutaka.');e.networkFailure=true;throw e;}
          if(!response.ok){const e=new Error(data.code==='rest_cookie_invalid_nonce'?'Prijava je istekla. Osveži stranicu i ponovo se prijavi.':data.message || 'Zahtev nije uspeo. Pokušaj ponovo.');e.code=data.code;throw e;}
          return data;
        }catch(e){if(!e.code)e.networkFailure=true;throw e;}finally{clearTimeout(timer);}
      };
      try{
        if(this.ajaxTransport)return await perform(true);
        try{return await perform(false);}catch(e){if(!e.networkFailure || !this.root.dataset.ajax)throw e;this.ajaxTransport=true;try{return await perform(true);}catch(fallback){fallback.networkFailure=true;throw fallback;}}
      }catch(e){
        if(e.networkFailure && !e.code)e.message=L.t('Veza je prekinuta. Proveri internet i pokušaj ponovo.');else e.message=L.text(e.message);
        throw e;
      }
    }
    key(guest = false,date = this.info.date) { return `session:v2:${!guest && this.account.authenticated ? 'user-' + this.account.user.id : 'guest'}:${this.mode}:${this.kind}:${this.mode === 'daily' ? date : 'practice'}`; }
    async refreshDay() {
      if (!this.info || (this.info.next_change && Date.now() + (this.clockOffset || 0) >= this.info.next_change * 1000)) {
        this.info = await this.request('bootstrap');
        if(this.info.config){const cfg=this.info.config;if(!cfg[this.mode+'_enabled'])this.mode=cfg.daily_enabled?'daily':'practice';}
        this.clockOffset = this.info.server_time ? this.info.server_time * 1000 - Date.now() : 0;
      }
    }
    async accountSession() {
      if (!this.root.dataset.ajax) { this.accountChecked = true; return; }
      const controller = new AbortController(), timer = setTimeout(() => controller.abort(),20000);
      try {
        const url = new URL(this.root.dataset.ajax,location.href); url.searchParams.set('action','snd_session');
        const response = await fetch(url.href,{ credentials:'same-origin',cache:'no-store',signal:controller.signal });
        const data = await response.json();
        if (!response.ok || typeof data.authenticated !== 'boolean' || (data.authenticated && (!data.user || !data.nonce))) throw new Error('Sesija naloga nije dostupna.');
        data.active = data.active && !Array.isArray(data.active) && typeof data.active === 'object' ? data.active : {};
        this.account = data; this.accountChecked = true;
      } catch (_) { throw new Error('Prijava nije mogla da se proveri. Osveži stranicu da bi se progres pravilno sačuvao.'); }
      finally { clearTimeout(timer); }
    }
    async boot() {
      try {
        await this.accountSession(); this.info = null; await this.refreshDay();
        if (this.mode === 'daily' && this.linkDate && this.linkDate !== this.info.date) this.notice = ['Link je za datum',this.linkDate + '.','Otvoren je današnji izazov; arhiva prethodnih dana nije dostupna.'].join(' ');
        await this.start(false);
      }
      catch (e) { this.root.innerHTML = L.html(`<div class="snd-load-error"><h2>Igra trenutno ne može da se učita.</h2><p>${esc(e.message)}</p><button class="snd-primary" data-cmd="reload">Pokušaj ponovo</button></div>`); this.root.querySelector('button').onclick = () => this.boot(); }
    }
    async start(fresh) {
      if (this.busy) return;
      this.busy = true; this.error = ''; const requestNumber = ++this.requestNumber;
      this.root.setAttribute('aria-busy', 'true');this.setBusy(true);
      try {
        await this.refreshDay();
        let result;
        const slot = this.kind + ':' + this.mode;
        const old = fresh ? null : (this.account.active[slot] ? {token:this.account.active[slot]} : storage.get(this.key()));
        if (old && typeof old.token === 'string') {
          try { result = await this.request('resume', { token: old.token }); }
          catch (e) { if (!['snd_expired', 'snd_token', 'snd_new_day','snd_owner'].includes(e.code)) throw e; storage.remove(this.key()); delete this.account.active[slot]; }
        }
        if (!result && !fresh && this.account.authenticated) {
          const guestKey = this.key(true), guest = storage.get(guestKey);
          if (guest && typeof guest.token === 'string') {
            try { result = await this.request('claim',{token:guest.token}); storage.remove(guestKey); }
            catch (e) { if (!['snd_expired','snd_token','snd_new_day','snd_owner'].includes(e.code)) throw e; storage.remove(guestKey); }
          }
        }
        if (!result) result = await this.request('start', { kind: this.kind, mode: this.mode, fresh:!!fresh });
        if (requestNumber !== this.requestNumber) return;
        this.accept(result); this.render();
      } catch (e) { this.error = e.message; if (this.state) { this.kind = this.state.kind; this.mode = this.state.mode; this.setRecovery(e); this.render(); } else throw e; }
      finally { this.busy = false; this.root.removeAttribute('aria-busy'); this.setBusy(false); }
    }
    accept(result) {
      this.token = result.token; this.state = result.state; this.kind = this.state.kind; this.mode = this.state.mode;
      if (result.progress) this.account.progress = result.progress;
      if (this.account.authenticated) this.account.active[this.kind + ':' + this.mode] = this.token;
      this.selected = null; this.recovery = null; this.mediaFailed = false; this.mediaAttempt = 0;
      if (this.state.mode === 'daily') this.info.date = this.state.date;
      this.canSave = storage.set(this.key(false,this.state.date), { token: this.token });
      storage.set('preferences', { kind: this.kind, mode: this.mode });
      if (this.state.stage === 'complete' && !this.account.authenticated) this.record();
    }
    async action(action, value) {
      if (this.busy) return;
      this.busy = true; this.error = ''; this.setBusy(true);const beforeRevision=this.state.revision;
      try { this.accept(await this.request('action', { token: this.token, revision: this.state.revision, action, value })); }
      catch (e) {
        this.error = e.message;
        // Recover a committed request after a timeout, duplicate tab, or lost response.
        if (!['snd_new_day', 'snd_expired', 'snd_token','snd_owner','rest_cookie_invalid_nonce'].includes(e.code)) {
          try { this.accept(await this.request('resume', { token: this.token }));if(e.networkFailure && this.state.revision>beforeRevision)this.error=''; } catch (_) {}
        } else { this.setRecovery(e); }
      }
      finally { this.busy = false; this.render(); this.focusPanel(); }
    }
    setBusy(on) {
      this.root.setAttribute('aria-busy', on ? 'true' : 'false');const loader=this.root.querySelector('.snd-game-loading'),shared=this.root.dataset.shell==='1' && typeof document.getElementById==='function'?document.getElementById('snd-page-loader'):null;if(shared)shared.hidden=!on;if(loader)loader.hidden=shared?true:!on;
      this.root.querySelectorAll('button[data-cmd], .snd-option, button[data-zoom]').forEach(b => { if (on) { b.dataset.wasDisabled = b.disabled ? '1' : '0'; b.disabled = true; } else if (b.dataset.wasDisabled !== undefined) { b.disabled = b.dataset.wasDisabled === '1'; delete b.dataset.wasDisabled; } });
    }
    async switchGame(kind, mode) {
      if (this.busy || (this.kind === kind && this.mode === mode)) return;
      this.kind = kind; this.mode = mode; this.recovery = null; this.notice = '';
      await this.start(false);
    }
    setRecovery(error) {
      if (['rest_cookie_invalid_nonce','snd_owner','rest_forbidden'].includes(error.code)) {
        this.recovery = {command:'renewsession',label:'Obnovi prijavu'}; return;
      }
      if (['snd_expired','snd_token','snd_new_day'].includes(error.code)) {
        this.recovery = {command:'restart',label:this.mode === 'practice' ? 'Nova partija vežbanja' : 'Otvori današnji izazov'};
        storage.remove(this.key(false,this.state ? this.state.date : this.info.date));
      }
    }
    async recoverSession() {
      if(this.recovering)return;this.recovering=true;
      try {
        await this.accountSession();
        if(!this.account.authenticated){location.assign(this.root.dataset.login);return;}
        const response=await this.request('resume',{token:this.token});this.error='';this.accept(response);this.render();
      } catch(e) {
        this.error=e.message;
        if(e.code==='snd_owner'){this.state=null;await this.boot();}else{this.setRecovery(e);this.render();}
      } finally {this.recovering=false;}
    }
    mediaUrl(url) { if(!this.mediaAttempt)return safeUrl(url);const value=new URL(url,location.href);value.searchParams.set('snd_retry',String(this.mediaAttempt));return safeUrl(value.href); }
    mediaProblem() {
      if(!this.state || this.state.stage!=='quiz' || this.state.question.answer)return;
      this.mediaFailed=true;
      this.root.querySelectorAll('.snd-media-img').forEach(img=>{img.hidden=true;});
      this.root.querySelectorAll('[data-choice], [data-zoom]').forEach(button=>{button.disabled=true;});
      const note=this.root.querySelector('.snd-media-error');if(note)note.hidden=false;
    }
    outline() { return this.info.outline.map(d => `<path d="${esc(d)}"/>`).join(''); }
    render() {
      const s = this.state; if (!s) return;
      const isDistrict = s.kind === 'district';
      this.root.innerHTML = L.html(`
        <header class="snd-header"><h1>${this.fixedKind ? isDistrict ? 'Okruzi' : 'Opštine i gradovi' : esc(L.config().app_name || 'Srbija na dlanu')}</h1><button class="snd-icon-button" data-cmd="help" aria-label="Kako se igra?" title="Kako se igra?">${icon('help')}</button></header>
        <nav class="snd-toolbar" aria-label="Podešavanja igre">
          ${this.fixedKind ? '' : `<div class="snd-tabs" role="group" aria-label="Izaberi igru"><button class="${isDistrict ? 'is-active' : ''}" data-cmd="district" aria-pressed="${isDistrict}">Okruzi</button><button class="${!isDistrict ? 'is-active' : ''}" data-cmd="municipality" aria-pressed="${!isDistrict}">Opštine i gradovi</button></div>`}
          <div class="snd-mode" role="group" aria-label="Režim igre"><button class="${s.mode === 'daily' ? 'is-active' : ''}" data-cmd="daily" aria-pressed="${s.mode === 'daily'}" ${this.info.config && !this.info.config.daily_enabled?'hidden':''}>Dnevni izazov</button><button class="${s.mode === 'practice' ? 'is-active' : ''}" data-cmd="practice" aria-pressed="${s.mode === 'practice'}" ${this.info.config && !this.info.config.practice_enabled?'hidden':''}>Vežbanje</button></div>
        </nav>
        <div class="snd-game-loading" role="status" hidden>${L.config().loader || ''}<span class="snd-sr-only">Učitavanje…</span></div>${this.pointsIntro()}<div class="snd-board">${this.visual()}<section class="snd-panel" tabindex="-1" aria-label="Trenutna runda">${this.panel()}</section></div>
        <div class="snd-error" role="alert" ${this.error ? '' : 'hidden'}>${esc(this.error)}${this.recovery ? `<button data-cmd="${esc(this.recovery.command)}">${esc(this.recovery.label)}</button>` : ''}</div>
        ${this.canSave === false && !this.account.authenticated ? '<p class="snd-storage-note">Pregledač ne dozvoljava čuvanje partije i statistike.</p>' : ''}
        ${this.notice ? `<p class="snd-notice" role="status">${esc(this.notice)}</p>` : ''}
        ${!s.scoring_rules && s.stage==='guess'?`<aside class="snd-update-notice"><p>Nova verzija donosi bodovanje i obnovljenu rundu zastava. Nastavi ovu partiju ili započni novo vežbanje.</p><button class="snd-text-button" data-cmd="again">${icon('refresh')}<span>Započni novo vežbanje</span></button></aside>`:''}
        ${s.scoring_rules && s.kind==='municipality' && s.stage==='guess' && (this.info.retired_ids || []).some(id=>(s.catalog || []).some(t=>t.id===id))?`<aside class="snd-update-notice"><p>Beogradske opštine su dodate u igru. Nastavi ovu partiju ili ih isprobaj u novoj.</p><button class="snd-text-button" data-cmd="again">${icon('refresh')}<span>Započni novo vežbanje</span></button></aside>`:''}
        <footer class="snd-footer"><p class="snd-save-status">${this.account.authenticated ? `<span>Progres se čuva na nalogu.</span><a href="${safeUrl(this.root.dataset.account)}">Moj nalog</a>` : `<span>Igraš kao gost.</span><a href="${safeUrl(this.root.dataset.login)}">Prijavi se</a><span>za čuvanje na nalogu.</span>`}</p>${L.config().show_sources===false?'':`<button data-cmd="sources">${icon('sources')}<span>Obuhvat i izvori</span></button>`}</footer>
        <div class="snd-modal-host"></div><div class="snd-announcer" aria-live="polite" aria-atomic="true"></div>`);
      this.bind();
      const panel=this.root.querySelector('.snd-panel');if(panel && panel.animate && !(typeof matchMedia==='function' && matchMedia('(prefers-reduced-motion: reduce)').matches))panel.animate([{opacity:.25,transform:'translateY(5px)'},{opacity:1,transform:'translateY(0)'}],{duration:200,easing:'ease-out'});
      const live=this.root.querySelector('.snd-announcer');
      if(live){const message=s.stage==='quiz'?L.t('Runda {round} od {total}: {title}',{round:s.question_index+2,total:s.question_count+1,title:s.question.title}):L.text(s.stage==='complete'?'Partija je završena.':s.stage==='reveal'?'Teritorija je otkrivena.':'Prepoznaj teritoriju.');setTimeout(()=>{if(this.root.querySelector('.snd-announcer')===live)live.textContent=message;},50);}
      this.setBusy(this.busy);
    }
    visual() {
      const s = this.state, q = s.question;
      if (s.stage === 'quiz' && q.image) {
        return `<div class="snd-visual snd-visual-photo"><div class="snd-photo-wrap"><img class="snd-landmark-photo snd-media-img" src="${this.mediaUrl(q.image)}" alt="Znamenitost koju treba prepoznati" loading="eager"></div><p class="snd-photo-credit">Foto: ${esc(q.credit)} · ${licenseUrl(q.license) ? `<a href="${safeUrl(licenseUrl(q.license))}" target="_blank" rel="noopener noreferrer">${esc(q.license)}</a>` : esc(q.license)}${q.image_source ? ` · ${sourceLink(q.image_source,'Izvor fotografije ↗')}` : ''}</p>${!q.answer && !q.image_source && s.source_help_enabled!==false ? '<button class="snd-text-button" data-cmd="source">Prikaži izvor fotografije (pomoć)</button>' : ''}${q.source_help_used ? '<p class="snd-round">Izvor fotografije je korišćen kao pomoć.</p>' : ''}</div>`;
      }
      if (s.stage === 'guess' || s.stage === 'quiz') {
        return `<div class="snd-visual ${s.stage === 'quiz' ? 'snd-visual-small' : ''}"><svg class="snd-shape" viewBox="0 0 360 360" role="img" aria-label="${s.stage === 'guess' ? 'Obris nepoznate teritorije; sever je gore' : 'Obris teritorije ' + esc(s.territory.name)}"><path d="${esc(s.shape)}"/></svg></div>`;
      }
      const attempts=new Map(s.guesses.map((g,i)=>[g.id,{...g,number:i+1}]));
      return `<div class="snd-visual snd-visual-context"><svg class="snd-context-map" data-map-kind="${esc(s.kind)}" viewBox="0 0 600 800" role="img" aria-label="Položaj teritorije ${esc(s.territory.name)} na mapi Srbije"><g class="snd-map-base">${this.outline()}</g>${(s.map || []).map(t => `<path class="${t.id === s.territory.id ? 'snd-map-target' : attempts.has(t.id) ? 'snd-map-last' : 'snd-map-cell'}" data-map-region="${esc(t.id)}" d="${esc(t.map_path)}"/>`).join('')}<g class="snd-map-markers" aria-hidden="true">${s.guesses.map((g,i)=>`<g data-map-attempt="${esc(g.id)}" class="${g.correct?'is-correct':''}" visibility="hidden"><line/><circle r="22"/><text text-anchor="middle" dy=".35em">${i+1}</text></g>`).join('')}${!s.solved?`<g data-map-answer="${esc(s.territory.id)}" class="is-target" visibility="hidden"><line/><circle r="27"/><path class="snd-target-symbol" d="m0-13 4 8 9 1-7 6 2 9-8-4-8 4 2-9-7-6 9-1Z"/></g>`:''}</g></svg><aside class="snd-target-answer"><span>Zadata teritorija</span><strong>${esc(s.territory.name)}</strong></aside><ol class="snd-map-caption" aria-label="Prethodni pokušaji">${s.guesses.map((g,i)=>`<li class="${g.correct?'snd-legend-target':'snd-legend-last'}"><span class="snd-map-number" aria-hidden="true">${i+1}</span>${esc(g.name)}</li>`).join('')}</ol></div>`;
    }
    panel() {
      const s = this.state;
      if (s.stage === 'guess') return this.guessPanel();
      if (s.stage === 'reveal') return `<h2>${s.solved?'Pogođeno':s.guesses.length>=s.max_guesses?'Nije pogođeno':'Odgovor je otkriven.'}</h2><p class="snd-panel-copy">${s.solved ? L.t('Pogođeno iz {count}. pokušaja.',{count:s.guesses.length}) : ''}</p><button class="snd-primary" data-cmd="next">${icon('next')}<span>${s.question_count ? 'Dodatna pitanja' : 'Rezultat'}</span></button><button class="snd-text-button" data-cmd="finish">${icon('finish')}<span>Završi partiju</span></button>`;
      if (s.stage === 'quiz') return this.quizPanel();
      return this.resultPanel();
    }
    guessPanel() {
      const s = this.state;
      return `<p class="snd-round-label">Runda 1/${s.question_count + 1}</p><div class="snd-question-heading"><h2>Koja je ovo teritorija?</h2><span class="snd-turn" aria-label="Pokušaj ${s.guesses.length + 1} od ${s.max_guesses}">Pokušaj ${s.guesses.length + 1}/${s.max_guesses}</span></div><form class="snd-guess-form"><label class="snd-sr-only" for="${this.root.id}-search">${s.kind === 'district' ? 'Naziv okruga ili Grad Beograd' : 'Naziv opštine ili grada'}</label><div class="snd-search-wrap"><input id="${this.root.id}-search" type="text" autocomplete="off" autocapitalize="off" spellcheck="false" placeholder="${s.kind === 'district' ? 'Unesi okrug…' : 'Unesi opštinu ili grad…'}" role="combobox" aria-expanded="false" aria-autocomplete="list" aria-controls="${this.root.id}-suggestions"><div id="${this.root.id}-suggestions" class="snd-suggestions" role="listbox" aria-label="Predložene teritorije" hidden></div></div><button class="snd-primary" type="submit" data-cmd="guess" disabled>Pogodi</button></form>${s.hint ? `<p class="snd-hint">${icon('hint')}${esc(s.hint_text)}</p>` : ''}<div class="snd-secondary-actions">${!s.hint && s.hint_enabled!==false && s.guesses.length >= (s.hint_after || 3) ? `<button class="snd-text-button" data-cmd="hint">${icon('hint')}<span>Prikaži pomoć</span></button>` : ''}<button class="snd-text-button" data-cmd="giveup">${icon('eye')}<span>Otkrij odgovor</span></button></div>${this.history()}`;
    }
    history() {
      const s = this.state;
      return `<ol class="snd-history" aria-label="Pokušaji">${Array.from({length:s.max_guesses},(_,i)=>{const g=s.guesses[i];return `<li class="${g?g.correct?'is-correct':'is-played':i===s.guesses.length?'is-empty is-current':'is-empty'}"${!g&&i===s.guesses.length?' aria-current="step"':''}><span class="snd-guess-number">${i+1}</span>${g?`<strong>${esc(g.name)}</strong>${g.correct?`<span class="snd-distance">Pogođeno</span><span class="snd-direction" aria-label="Tačan odgovor">${icon('correct')}</span>`:`<span class="snd-distance">${g.km===0?'&lt; 1':g.km} km</span><span class="snd-direction" role="img" aria-label="${esc(g.direction.name)}" title="${esc(g.direction.name)}">${directionIcon(g.direction.arrow)}</span>`}`:`<span class="snd-sr-only">${esc(L.t('Pokušaj {count}: još nije odigran.',{count:i+1}))}</span><span class="snd-empty-line" aria-hidden="true"></span>`}</li>`;}).join('')}</ol>`;
    }
    questionOption(q,o,a) {
      const status=a?o.id===q.correct?'is-correct':o.id===a.choice?'is-wrong':'is-muted':'',flag=q.type==='flag';
      const badge=a && (o.id===q.correct || o.id===a.choice)?`<span class="snd-answer-badge ${o.id===q.correct?'is-correct':'is-wrong'}" role="img" aria-label="${o.id===q.correct?'Tačan odgovor':'Netačan odgovor'}">${icon(o.id===q.correct?'correct':'wrong')}</span>`:'';
      const choice=`<button class="snd-option ${flag?'snd-flag-option':''} ${status}" data-choice="${esc(o.id)}" ${a || this.mediaFailed?'disabled':''}>${o.image?`<img class="snd-media-img" src="${this.mediaUrl(o.image)}" alt="" loading="eager">`:''}<span class="snd-option-label"><span>${esc(o.label)}</span>${badge}</span></button>`;
      return flag?`<div class="snd-flag-card ${status}">${choice}<button type="button" class="snd-flag-zoom" aria-haspopup="dialog" data-zoom="${esc(o.id)}" aria-label="${esc(L.t('Uvećaj: {label}',{label:o.label}))}" ${this.mediaFailed?'disabled':''}>${icon('zoom')}</button></div>`:choice;
    }
    zoomFlag(id) {
      const q=this.state && this.state.question;if(!q || q.type!=='flag')return;
      const option=q.options.find(o=>o.id===id);if(!option || !option.image)return;
      this.modal(L.t('Uvećana slika: {label}',{label:option.label}),`<figure class="snd-flag-preview"><img src="${safeUrl(option.image)}" alt="${esc(option.label)}"><figcaption>${esc(option.label)}</figcaption></figure>`,'snd-flag-modal');
    }
    quizPanel() {
      const s=this.state,q=s.question,a=q.answer,media=Boolean(q.image || q.type==='flag');
      return `<p class="snd-round">${esc(s.territory.name)} · Runda ${s.question_index+2}/${s.question_count+1}</p><h2 class="snd-question-title">${esc(q.title)}</h2>${q.subtitle ? `<p class="snd-panel-copy">${esc(q.subtitle)}</p>` : ''}
        <div class="snd-options ${q.type==='flag'?'snd-flag-options':''}">${q.options.map(o=>this.questionOption(q,o,a)).join('')}</div>
        ${media&&!a?`<div class="snd-media-error" role="status" ${this.mediaFailed?'':'hidden'}><p>${s.media_fallback!==false?'Slika nije mogla da se učita. Pokušaj ponovo ili otvori zamensko geografsko pitanje.':'Slika nije mogla da se učita. Pokušaj ponovo.'}</p><button data-cmd="retrymedia">Ponovo učitaj slike</button>${s.media_fallback!==false?'<button data-cmd="media_unavailable">Zamensko pitanje</button>':''}</div>`:''}
        ${a?`<div class="snd-explanation ${a.skipped?'is-skipped':a.correct?'is-correct':'is-wrong'}" role="status"><strong>${a.skipped?'Preskočeno — tačan odgovor':a.correct?'Tačno.':'Netačno.'}</strong><p>${esc(q.explanation)}</p>${questionSources(q)}${this.credits(q)}</div><button class="snd-primary" data-cmd="next">${icon('next')}<span>${s.question_index+1===s.question_count?'Rezultat':'Dalje'}</span></button>`:`<button class="snd-text-button" data-cmd="skip">${icon('skip')}<span>Preskoči pitanje</span></button>`}`;
    }
    credits(q) {
      return q.credits && q.credits.length ? `<details class="snd-flag-credits"><summary>Autori i licence zastava</summary>${q.credits.map(c=>`<p>${esc(c.label)}: ${esc(c.credit)} · ${esc(c.license)} ${sourceLink(c.source,'Izvor ↗')}</p>`).join('')}</details>` : '';
    }
    reviewItem(q) {
      const a=q.answer || {},status=a.skipped?'Preskočeno':a.correct?'Tačno':'Netačno';
      return `<article><h3>${esc(q.title || 'Dodatno pitanje')}</h3><p class="snd-review-status">${status}</p><p>Tvoj odgovor: ${a.skipped?'Nisi odgovorio/la.':esc(q.choice_label || 'Odgovor nije sačuvan u starijoj verziji.')}</p><p class="snd-review-correct">Tačan odgovor: ${esc(q.correct_label)}</p>${q.correct_image?`<img src="${safeUrl(q.correct_image)}" alt="${esc(q.correct_label)}" loading="lazy">`:''}<p>${esc(q.explanation)}</p>${q.source_help_used?'<p>Izvor fotografije korišćen je kao pomoć.</p>':''}${q.technical_replacement?'<p>Geografsko pitanje zamenilo je sliku koja nije učitana.</p>':''}${q.credit?`<p class="snd-photo-credit">${esc(q.credit)} · ${esc(q.license)}</p>`:''}${questionSources(q)}${this.credits(q)}</article>`;
    }
    record() {
      const s = this.state;
      const all = storage.get('stats', { district: { days:[], discovered:[] }, municipality: { days:[], discovered:[] } });
      const stats = kindStats(all,s.kind);
      if (!stats.discovered.includes(s.territory.id)) stats.discovered.push(s.territory.id);
      if (s.mode === 'daily' && !stats.days.includes(s.date)) stats.days.push(s.date);
      stats.days = stats.days.sort().slice(-400); all[s.kind] = stats; storage.set('stats', all);
    }
    streak() {
      if (this.account.authenticated) return kindStats(this.account.progress,this.state.kind).streak;
      const stats = kindStats(storage.get('stats', {}),this.kind);
      let day = this.info.date, count = 0;
      if (!stats.days.includes(day)) day = this.previousDay(day);
      while (stats.days.includes(day)) { count++; day = this.previousDay(day); }
      return count;
    }
    previousDay(day) { const d = new Date(day + 'T12:00:00Z'); d.setUTCDate(d.getUTCDate() - 1); return d.toISOString().slice(0,10); }
    pointsIntro() {
      const s=this.state,r=s.scoring_rules;if(!r)return '';
      const base=s.question_count?r.territory:100;
      return `<div class="snd-rank-intro"><details><summary>Bodovanje: 0–100</summary><p>${esc(L.t('Teritorija: do {base} poena. Svaki naredni pokušaj umanjuje taj deo za {step}.',{base,step:r.step}))} ${esc(L.t('Dodatna pitanja: do {quiz} poena, ravnomerno po tačnom odgovoru.',{quiz:100-base}))} ${esc(L.t('Pomoć pri pogađanju oduzima {hint} poena. Pitanje uz pomoć izvora ne donosi poene.',{hint:r.hint}))}</p><a href="${safeUrl(L.config().ranking_url)}">Rang lista</a></details></div>`;
    }
    pointsResult() {
      const p=this.state.points;if(!p)return '<p class="snd-result-meta">Ova partija je započeta pre uvođenja bodovanja.</p>';
      const ranked=this.state.rank_eligible;
      return `<section class="snd-points-result" aria-label="Bodovi"><strong>${p.total}<small> / 100</small></strong><div class="snd-points-breakdown"><span>${esc(L.t('Teritorija: {points}',{points:p.territory}))}</span><span>${esc(L.t('Kviz: {points}',{points:p.quiz}))}</span>${p.penalty?`<span>${esc(L.t('Pomoć: −{points}',{points:p.penalty}))}</span>`:''}</div><p>${ranked?'Rezultat je upisan na rang listu.':this.account.authenticated?'Ova partija ne ulazi u rang listu.':'Za rangiranje se prijavi pre početka nove partije.'} <a href="${safeUrl(L.config().ranking_url)}">Rang lista</a></p></section>`;
    }
    resultPanel() {
      const s=this.state,stats=kindStats(this.account.authenticated?this.account.progress:storage.get('stats',{}),s.kind);
      const help=s.hint || s.source_help?'Pomoć: korišćena':'Pomoć: nije korišćena',discovered=stats.discovered.filter(id=>!(this.info.retired_ids || []).includes(id));
      return `<h2>Rezultat</h2>${this.pointsResult()}<p class="snd-outcome">${esc(outcome(s))}</p><div class="snd-score-grid"><div><span>Teritorija</span><strong class="${s.solved?'':'snd-score-word'}">${s.solved?s.guesses.length+'<small>/'+s.max_guesses+'</small>':s.guesses.length>=s.max_guesses?'Nije pogođeno':'Otkriveno'}</strong></div><div><span>Kviz</span><strong>${s.score}<small>/${s.question_count}</small></strong></div></div><p class="snd-result-meta">${help}${s.mode==='daily'?' · Niz igranja: '+formatDays(this.streak()):''}</p><button class="snd-primary" data-cmd="share">${icon('share')}<span>Podeli rezultat</span></button><button class="snd-secondary" data-cmd="again">${icon('refresh')}<span>${s.mode==='daily'?'Vežbaj':'Nova partija'}</span></button><details class="snd-review"><summary>Odgovori i statistika</summary><p>Upoznato: ${discovered.length}/${this.info.counts[s.kind]} teritorija.</p>${s.review.map(q=>this.reviewItem(q)).join('')}</details>`;
    }
    mapAttempts() {
      const svg=this.root.querySelector('.snd-context-map');if(!svg)return;
      const matrix=svg.getScreenCTM(),scale=matrix?Math.hypot(matrix.a,matrix.b):.5;
      if(!scale)return;
      const compact=this.state.kind==='municipality',placed=[];
      [...svg.querySelectorAll('[data-map-attempt],[data-map-answer]')].sort((a,b)=>Number(b.hasAttribute('data-map-answer'))-Number(a.hasAttribute('data-map-answer'))).forEach(marker=>{
        const shape=svg.querySelector(`[data-map-region="${(marker.dataset.mapAttempt || marker.dataset.mapAnswer)}"]`);if(!shape || typeof shape.getBBox!=='function')return;
        const answer=marker.hasAttribute('data-map-answer'),radius=(compact?(answer?8:6):(answer?11:9))/scale;
        marker.querySelector('circle').setAttribute('r',radius);
        const text=marker.querySelector('text');if(text)text.style.fontSize=(compact?9:11)/scale+'px';
        const star=marker.querySelector('.snd-target-symbol');if(star)star.setAttribute('transform',`scale(${radius/22})`);
        const b=shape.getBBox(),center={x:b.x+b.width/2,y:b.y+b.height/2},candidates=[];
        for(let row=1;row<12;row++)for(let col=1;col<12;col++){
          const point={x:b.x+b.width*col/12,y:b.y+b.height*row/12};
          const probe=svg.createSVGPoint();probe.x=point.x;probe.y=point.y;
          if(typeof shape.isPointInFill!=='function' || shape.isPointInFill(probe))candidates.push(point);
        }
        candidates.sort((a,b)=>Math.hypot(a.x-center.x,a.y-center.y)-Math.hypot(b.x-center.x,b.y-center.y));
        const origin=candidates[0] || center,small=Math.min(b.width,b.height)<radius*3;
        const outside=p=>p.x+radius<b.x || p.x-radius>b.x+b.width || p.y+radius<b.y || p.y-radius>b.y+b.height;
        let point=origin;
        for(let ring=0;ring<12;ring++){
          const distance=(small?Math.max(b.width,b.height)/2+radius+4/scale:0)+ring*(radius*2+3/scale);
          const choices=ring===0&&!small?[origin]:Array.from({length:24},(_,i)=>({x:origin.x+Math.cos(i*Math.PI/12)*distance,y:origin.y+Math.sin(i*Math.PI/12)*distance}));
          const free=choices.find(p=>p.x>=radius+3 && p.x<=597-radius && p.y>=radius+3 && p.y<=797-radius && (!small || outside(p)) && placed.every(other=>Math.hypot(p.x-other.x,p.y-other.y)>=radius+other.radius+3/scale));
          if(free){point=free;break;}
        }
        placed.push({...point,radius});marker.setAttribute('transform',`translate(${point.x} ${point.y})`);marker.removeAttribute('visibility');
        const line=marker.querySelector('line'),dx=origin.x-point.x,dy=origin.y-point.y,distance=Math.hypot(dx,dy);
        line.setAttribute('x1',distance>radius?dx*radius/distance:0);line.setAttribute('y1',distance>radius?dy*radius/distance:0);
        line.setAttribute('x2',dx);line.setAttribute('y2',dy);
      });
    }
    bind() {
      if(this.mapResize)this.mapResize.disconnect();
      this.mapAttempts();
      const map=this.root.querySelector('.snd-context-map');
      if(map && typeof ResizeObserver!=='undefined'){this.mapResize=new ResizeObserver(()=>this.mapAttempts());this.mapResize.observe(map);}
      this.root.querySelectorAll('[data-zoom]').forEach(button=>button.addEventListener('click',event=>{event.preventDefault();if(!this.busy)this.zoomFlag(button.dataset.zoom);}));
      this.root.querySelectorAll('[data-cmd]').forEach(b => b.addEventListener('click', async event => {
        const command = b.dataset.cmd;
        if (command === 'guess') return;
        event.preventDefault(); if (this.busy) return;
        if (['district','municipality'].includes(command)) return this.switchGame(command, this.mode);
        if (['daily','practice'].includes(command)) return this.switchGame(this.kind, command);
        if (command === 'again') { this.mode = 'practice'; return this.start(true); }
        if(command==='restart'){this.recovery=null;this.notice='';delete this.account.active[this.kind+':'+this.mode];this.state=null;return this.boot();}
        if(command==='renewsession')return this.recoverSession();
        if(command==='retrymedia'){this.mediaFailed=false;this.mediaAttempt=Date.now();this.render();return;}
        if (command === 'help') return this.help();
        if (command === 'sources') return this.sources();
        if (command === 'share') return this.share();
        return this.action(command);
      }));
      this.root.querySelectorAll('[data-choice]').forEach(b => b.addEventListener('click', () => this.action('answer', b.dataset.choice)));
      this.root.querySelectorAll('.snd-media-img').forEach(image=>{
        image.addEventListener('error',()=>this.mediaProblem());
        if(image.complete && image.naturalWidth===0)this.mediaProblem();
      });
      const form = this.root.querySelector('.snd-guess-form');
      if (form) {
        const input = form.querySelector('input'), list = form.querySelector('[role=listbox]'), submit = form.querySelector('button');
        let matches = [], active = -1,gesture=null,interacting=false;
        const close = () => { list.hidden = true; input.setAttribute('aria-expanded','false'); input.removeAttribute('aria-activedescendant'); active = -1; };
        const choose = index => { if (!matches[index]) return; this.selected = matches[index]; input.value = this.selected.name; submit.disabled = false; input.focus(); close(); };
        const display = () => {
          const query = normalize(input.value); const guessed = new Set(this.state.guesses.map(g => g.id));
          const found=this.state.catalog.filter(t=>(!query || [t.name,...(t.aliases || [])].some(n=>normalize(n).includes(query))));
          matches=found.filter(t=>!guessed.has(t.id)).sort((a,b)=>a.name.localeCompare(b.name,L.config().language==='sr'?'sr-Latn':L.config().language || 'sr-Latn'));
          const alreadyTried=found.length>0 && matches.length===0;
          active = -1; input.removeAttribute('aria-activedescendant');
          list.innerHTML = L.html(matches.length ? matches.map((t,i) => `<div role="option" id="${this.root.id}-option-${i}" aria-selected="false" data-index="${i}">${esc(t.name)}<span aria-hidden="true">${icon('enter')}</span></div>`).join('') : `<div class="snd-no-matches">${alreadyTried?'Ovu teritoriju si već pokušao/la.':'Nema rezultata. Probaj drugi naziv.'}</div>`);
          list.hidden = false; input.setAttribute('aria-expanded','true');
          list.querySelectorAll('[role=option]').forEach(option=>option.addEventListener('click',()=>{
            if(gesture && gesture.moved)return;choose(Number(option.dataset.index));
          }));
        };
        list.addEventListener('pointerdown',event=>{
          gesture={x:event.clientX,y:event.clientY,moved:false};interacting=true;
          if(event.pointerType==='mouse' && event.button===0)event.preventDefault();
        });
        list.addEventListener('pointermove',event=>{if(gesture && touchMoved(gesture,event))gesture.moved=true;});
        list.addEventListener('pointerup',()=>{setTimeout(()=>{interacting=false;},0);});
        list.addEventListener('pointercancel',()=>{if(gesture)gesture.moved=true;interacting=false;});
        input.addEventListener('focus', display);
        input.addEventListener('input', () => { this.selected = null; submit.disabled = true; display(); });
        input.addEventListener('blur', () => setTimeout(()=>{if(!interacting)close();},150));
        input.addEventListener('keydown', event => {
          if (['ArrowDown','ArrowUp'].includes(event.key)) {
            event.preventDefault(); if (list.hidden) display(); if (!matches.length) return;
            active = nextSuggestionIndex(active,matches.length,event.key);
            list.querySelectorAll('[role=option]').forEach((o,i) => o.setAttribute('aria-selected', String(i === active)));
            const item = list.querySelector(`[data-index="${active}"]`); if (item) { input.setAttribute('aria-activedescendant',item.id); item.scrollIntoView({block:'nearest'}); }
          } else if (event.key === 'Enter' && !list.hidden && active >= 0) { event.preventDefault(); choose(active); }
          else if (event.key === 'Enter' && !this.selected) {
            const exact = matches.findIndex(t => [t.name,...(t.aliases || [])].some(n => normalize(n) === normalize(input.value)));
            if (exact >= 0) { event.preventDefault(); choose(exact); }
          } else if (event.key === 'Escape') close();
        });
        form.addEventListener('submit', event => { event.preventDefault(); if (this.selected) this.action('guess',this.selected.id); });
      }
    }
    focusPanel() { const panel = this.root.querySelector('.snd-panel'); if (panel) panel.focus({ preventScroll:true }); }
    modal(title, content, className='') {
      const host = this.root.querySelector('.snd-modal-host'), before = document.activeElement;
      host.innerHTML = L.html(`<div class="snd-modal-backdrop"><section class="snd-modal ${esc(className)}" role="dialog" aria-modal="true" aria-labelledby="${this.root.id}-modal-title" tabindex="-1"><button type="button" class="snd-modal-close" aria-label="Zatvori">${icon('wrong')}</button><h2 id="${this.root.id}-modal-title">${esc(title)}</h2>${content}</section></div>`);
      const modal = host.querySelector('.snd-modal'), close = () => { host.innerHTML = ''; if (before) before.focus(); };
      host.querySelector('.snd-modal-close').onclick = close;
      host.querySelector('.snd-modal-backdrop').onclick = event => { if (event.target === event.currentTarget) close(); };
      modal.onkeydown = event => {
        if (event.key === 'Escape') { event.preventDefault(); close(); }
        if (event.key === 'Tab') {
          const items = [...modal.querySelectorAll('a[href],button,textarea,input')].filter(el => !el.disabled), first = items[0], last = items[items.length-1];
          if (event.shiftKey && (document.activeElement === first || document.activeElement === modal)) { event.preventDefault(); last.focus(); }
          else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
        }
      };
      modal.focus();
    }
    help() {
      const s=this.state;
      const intro=L.t('Prepoznaj teritoriju iz najviše {count} pokušaja.',{count:s.max_guesses});
      const hint=s.hint_enabled!==false?L.t('Pomoć je dostupna posle {count} promašaja.',{count:s.hint_after || 3}):'';
      this.modal('Kako se igra?', `<ol><li>${esc(intro)}</li><li>Izaberi naziv iz pretrage. Prihvaćeni su latinica, ćirilica i unos bez kvačica.</li><li>Strelica i kilometri vode od tvog odgovora ka traženoj teritoriji. Sever je gore.</li>${hint?`<li>${esc(hint)}</li>`:''}${s.question_count?'<li>Posle pogađanja slede dodatna pitanja. Možeš ih odigrati, preskočiti ili odmah završiti partiju.</li>':''}</ol><p>Za mesta bez potvrđene zastave ili fotografije dobijaš jasno označeno geografsko pitanje. Autor i licenca slike uvek su vidljivi.</p><p>Dnevni izazov je isti za sve i menja se u ponoć po vremenu u Srbiji. Vežbanje daje nasumične partije. Niz igranja broji završene dnevne partije, uključujući one sa otkrivenim odgovorom i preskočenim kvizom.</p><p>Prijavljenim igračima partija i rezultati čuvaju se na nalogu. Gosti čuvaju rezultate u ovom pregledaču. Deljenje ne otkriva rešenje.</p>`);
    }
    sources() { this.modal('Mapa, podaci i slike', `<p>${esc(this.info.coverage)}</p><p>Obrisi su pojednostavljeni radi prikaza. Udaljenost se računa između geometrijskih centara, ne između granica ili administrativnih sedišta.</p><ul>${this.info.sources.map(s => `<li>${sourceLink(s.url,s.label)}<br><small>${esc(s.license)}</small></li>`).join('')}</ul><p>Uz odgovor na pitanje prikazan je njegov izvor. Fotografije i zastave imaju navedene autore i licence. Kada slika nije dostupna, dobijaš označeno geografsko pitanje.</p><p>${sourceLink('https://github.com/cvjetan23/Srbija-na-dlanu/blob/main/srbija-na-dlanu/FLAGS.md','Sve zastave: autori, licence i izvori ↗')}</p>`); }
    shareUrl() {
      const s=this.state,base=this.root.dataset[s.kind==='district'?'districtUrl':'municipalityUrl'] || location.href;
      const url=new URL(base,location.href);for(const key of ['mode','date','kind','token','_wpnonce','key','login','action','okrug','pretraga'])url.searchParams.delete(key);url.hash='';
      url.searchParams.set('mode',s.mode);if(s.mode==='daily')url.searchParams.set('date',s.date);
      if(!this.fixedKind)url.searchParams.set('kind',s.kind);
      return url.href;
    }
    async share() {
      const s=this.state;
      const marks=s.guesses.map(g=>g.correct?'🟩':'⬜').join('') || '↳';
      const review=s.review.map(r=>r.answer.correct?'🟩':r.answer.skipped?'⬜':'🟨').join('');
      const lines=[
        [(L.config().app_name || 'Srbija na dlanu')+' ·',L.text(s.kind==='district'?'Okruzi':'Opštine i gradovi')].join(' '),
        s.mode==='daily'?['Dnevni izazov #'+s.number,'·',s.date].join(' '):'Vežbanje',
        [marks,outcome(s),s.solved?s.guesses.length+'/'+s.max_guesses:''].filter(Boolean).join(' '),
        L.t('Kviz {marks} {score}',{marks:review,score:s.score+'/'+s.question_count}),
        ...(s.points?[L.t('Poeni: {points}/100',{points:s.points.total})]:[]),
        s.hint || s.source_help?'Pomoć: korišćena':'Pomoć: nije korišćena',
        this.shareUrl()
      ];
      const text=lines.map(line=>L.text(line)).join('\n');
      try { await navigator.clipboard.writeText(text); this.modal('Rezultat je kopiran', '<p>Možeš da ga pošalješ prijateljima. Rešenje nije uključeno.</p>'); }
      catch (_) { this.modal('Podeli rezultat', `<p>Kopiraj tekst i pošalji prijateljima:</p><textarea class="snd-share-text" readonly rows="8" aria-label="Rezultat za kopiranje">${esc(text)}</textarea>`); const area = this.root.querySelector('textarea'); area.focus(); area.select(); }
    }
  }
  document.querySelectorAll('.snd-game[data-api]').forEach(root => new Game(root));
  // Pure helpers are also available to automated checks without a browser.
  if (typeof module !== 'undefined' && module.exports) module.exports = { normalize, Game, formatDays, nextSuggestionIndex, touchMoved, kindStats, outcome };
})();
