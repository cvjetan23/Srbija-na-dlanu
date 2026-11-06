(function () {
  'use strict';
  const esc = value => String(value == null ? '' : value).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  const cyr = { а:'a',б:'b',в:'v',г:'g',д:'d',ђ:'dj',е:'e',ж:'z',з:'z',и:'i',ј:'j',к:'k',л:'l',љ:'lj',м:'m',н:'n',њ:'nj',о:'o',п:'p',р:'r',с:'s',т:'t',ћ:'c',у:'u',ф:'f',х:'h',ц:'c',ч:'c',џ:'dz',ш:'s' };
  const normalize = value => String(value).toLowerCase().replace(/[а-яђјљњћџ]/g, c => cyr[c] || c).replace(/đ/g, 'dj').normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/[^a-z0-9 ]/g, ' ').replace(/\s+/g, ' ').trim();
  const storage = {
    get(key, fallback = null) { try { const value = localStorage.getItem('snd:v1:' + key); return value ? JSON.parse(value) : fallback; } catch (_) { return fallback; } },
    set(key, value) { try { localStorage.setItem('snd:v1:' + key, JSON.stringify(value)); return true; } catch (_) { return false; } },
    remove(key) { try { localStorage.removeItem('snd:v1:' + key); } catch (_) {} }
  };
  const icons = {
    compass: '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.4"/><path d="m16 8-2.6 5.4L8 16l2.6-5.4L16 8Z" fill="currentColor"/><path d="M12 1v3m0 16v3M1 12h3m16 0h3" stroke="currentColor"/></svg>',
    arrow: '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14m-5-5 5 5-5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
    pin: '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M19 10c0 5-7 11-7 11S5 15 5 10a7 7 0 1 1 14 0Z" stroke="currentColor" stroke-width="1.5"/><circle cx="12" cy="10" r="2.5" stroke="currentColor" stroke-width="1.5"/></svg>',
    search: '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5" stroke="currentColor" stroke-width="1.6"/><path d="m16 16 4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>'
  };
  const safeUrl = value => { try { const u = new URL(value, location.href); return ['http:', 'https:'].includes(u.protocol) ? esc(u.href) : ''; } catch (_) { return ''; } };
  const sourceLink = (url, label = 'Izvor podatka ↗') => url ? `<a class="snd-source" href="${safeUrl(url)}" target="_blank" rel="noopener noreferrer">${esc(label)}</a>` : '';
  const licenseUrl = label => {
    const match = /^CC BY(-SA)? (\d+\.\d+)$/.exec(label || '');
    if (match) return `https://creativecommons.org/licenses/by${match[1] ? '-sa' : ''}/${match[2]}/`;
    if (label === 'CC0') return 'https://creativecommons.org/publicdomain/zero/1.0/';
    if (['Public domain','PDM','PD'].includes(label)) return 'https://creativecommons.org/publicdomain/mark/1.0/';
    return '';
  };

  class Game {
    constructor(root) {
      this.root = root; this.api = root.dataset.api; this.assets = root.dataset.assets;
      const preferences = storage.get('preferences', {});
      this.kind = preferences.kind === 'municipality' ? 'municipality' : 'district';
      this.mode = preferences.mode === 'practice' ? 'practice' : 'daily';
      this.state = null; this.token = null; this.busy = false; this.requestNumber = 0;
      this.selected = null; this.error = ''; this.boot();
    }
    async request(route, body) {
      const controller = new AbortController(); const timer = setTimeout(() => controller.abort(), 20000);
      try {
        const response = await fetch(this.api + route, { method: body ? 'POST' : 'GET', credentials: 'same-origin', cache: 'no-store', headers: body ? { 'Content-Type': 'application/json' } : {}, body: body ? JSON.stringify(body) : undefined, signal: controller.signal });
        let data; try { data = await response.json(); } catch (_) { throw new Error('WordPress nije vratio odgovor igre. Proveri REST API i dodatke za keširanje.'); }
        if (!response.ok) { const e = new Error(data.message || 'Zahtev nije uspeo. Pokušaj ponovo.'); e.code = data.code; throw e; }
        return data;
      } catch (e) { if (e.name === 'AbortError') { throw new Error('Odgovor kasni. Pokušaj ponovo; prethodni potez će biti sačuvan ako je obrađen.'); } throw e; }
      finally { clearTimeout(timer); }
    }
    key() { return `session:${this.mode}:${this.kind}:${this.mode === 'daily' ? this.info.date : 'practice'}`; }
    async boot() {
      try { this.info = await this.request('bootstrap'); await this.start(false); }
      catch (e) { this.root.innerHTML = `<div class="snd-load-error"><span class="snd-kicker">SRBIJA NA DLANU</span><h2>Igra trenutno ne može da se učita.</h2><p>${esc(e.message)}</p><button class="snd-primary" data-cmd="reload">Pokušaj ponovo</button></div>`; this.root.querySelector('button').onclick = () => this.boot(); }
    }
    async start(fresh) {
      if (this.busy) return;
      this.busy = true; this.error = ''; const requestNumber = ++this.requestNumber;
      this.root.setAttribute('aria-busy', 'true');
      try {
        let result; const old = fresh ? null : storage.get(this.key());
        if (old && typeof old.token === 'string') {
          try { result = await this.request('resume', { token: old.token }); }
          catch (e) { if (!['snd_expired', 'snd_token', 'snd_new_day'].includes(e.code)) throw e; storage.remove(this.key()); }
        }
        if (!result) result = await this.request('start', { kind: this.kind, mode: this.mode });
        if (requestNumber !== this.requestNumber) return;
        this.accept(result); this.render();
      } catch (e) { this.error = e.message; if (this.state) { this.kind = this.state.kind; this.mode = this.state.mode; this.render(); } else throw e; }
      finally { this.busy = false; this.root.removeAttribute('aria-busy'); this.setBusy(false); }
    }
    accept(result) {
      this.token = result.token; this.state = result.state; this.kind = this.state.kind; this.mode = this.state.mode;
      this.selected = null;
      this.canSave = storage.set(this.key(), { token: this.token });
      storage.set('preferences', { kind: this.kind, mode: this.mode });
      if (this.state.stage === 'complete') this.record();
    }
    async action(action, value) {
      if (this.busy) return;
      this.busy = true; this.error = ''; this.setBusy(true);
      try { this.accept(await this.request('action', { token: this.token, revision: this.state.revision, action, value })); }
      catch (e) {
        this.error = e.message;
        // Recover a committed request after a timeout, duplicate tab, or lost response.
        if (!['snd_new_day', 'snd_expired', 'snd_token'].includes(e.code)) {
          try { this.accept(await this.request('resume', { token: this.token })); } catch (_) {}
        } else { storage.remove(this.key()); this.expired = true; }
      }
      finally { this.busy = false; this.render(); this.focusPanel(); }
    }
    setBusy(on) {
      this.root.setAttribute('aria-busy', on ? 'true' : 'false');
      this.root.querySelectorAll('button[data-cmd], .snd-option').forEach(b => { if (on) { b.dataset.wasDisabled = b.disabled ? '1' : '0'; b.disabled = true; } else if (b.dataset.wasDisabled !== undefined) { b.disabled = b.dataset.wasDisabled === '1'; delete b.dataset.wasDisabled; } });
    }
    async switchGame(kind, mode) {
      if (this.busy || (this.kind === kind && this.mode === mode)) return;
      this.kind = kind; this.mode = mode; this.expired = false;
      await this.start(false);
    }
    outline() { return this.info.outline.map(d => `<path d="${esc(d)}"/>`).join(''); }
    render() {
      const s = this.state; if (!s) return;
      const isDistrict = s.kind === 'district';
      const date = new Intl.DateTimeFormat('sr-Latn-RS', { day: 'numeric', month: 'long', year: 'numeric', timeZone: 'Europe/Belgrade' }).format(new Date(s.date + 'T12:00:00+01:00'));
      const step = s.stage === 'guess' || s.stage === 'reveal' ? 0 : s.stage === 'complete' ? 4 : s.question_index + 1;
      this.root.innerHTML = `
        <header class="snd-header"><a class="snd-brand" href="#${esc(this.root.id)}" aria-label="Srbija na dlanu">${icons.compass}<span>SRBIJA<span class="snd-brand-small">NA DLANU</span></span></a><div class="snd-header-right"><span class="snd-live-dot"></span><span>SVAKI DAN, NOVO OTKRIĆE</span><button class="snd-icon-button" data-cmd="help" aria-label="Kako se igra?">?</button></div></header>
        <div class="snd-intro"><div><div class="snd-kicker"><span class="snd-small-line"></span> MALO GEOGRAFIJE. VELIKO OTKRIĆE.</div><h1>Koliko poznaješ <em>Srbiju?</em></h1><p>Prepoznaj obris. Prati tragove. Upoznaj jedan novi kraj.</p></div><div class="snd-intro-note"><span>${icons.pin}</span><p>Jedna teritorija.<br><strong>Cela mala avantura.</strong></p></div></div>
        <div class="snd-toolbar"><div class="snd-tabs" role="group" aria-label="Izaberi igru"><button class="${isDistrict ? 'is-active' : ''}" data-cmd="district" aria-pressed="${isDistrict}">Okruzi <span>${this.info.counts.district}</span></button><button class="${!isDistrict ? 'is-active' : ''}" data-cmd="municipality" aria-pressed="${!isDistrict}">Opštine i gradovi <span>${this.info.counts.municipality}</span></button></div><div class="snd-mode" role="group" aria-label="Režim igre"><button class="${s.mode === 'daily' ? 'is-active' : ''}" data-cmd="daily" aria-pressed="${s.mode === 'daily'}">Dnevni izazov</button><button class="${s.mode === 'practice' ? 'is-active' : ''}" data-cmd="practice" aria-pressed="${s.mode === 'practice'}">Vežbanje</button></div></div>
        <main class="snd-board"><div class="snd-board-top"><div><span class="snd-live-dot"></span><strong>${s.mode === 'daily' ? `Dnevni izazov #${s.number}` : 'Slobodno istraživanje'}</strong><span class="snd-top-date">${esc(date)}</span></div><span class="snd-top-label">${isDistrict ? 'OKRUZI' : 'OPŠTINE I GRADOVI'} <span>↗</span></span></div>
        <div class="snd-steps" aria-label="Napredak partije">${['Obris', 'Stanovništvo / položaj', isDistrict ? 'Sedište / geografija' : 'Pripadnost / geografija', 'Istraživanje'].map((label, i) => `<div class="${i === step ? 'is-active' : i < step ? 'is-done' : ''}"><span>${i < step ? '✓' : String(i + 1).padStart(2, '0')}</span><span>${label}</span></div>`).join('')}</div>
        <div class="snd-play-area">${this.visual()}<section class="snd-panel" tabindex="-1" aria-label="Trenutna runda">${this.panel()}</section></div></main>
        <div class="snd-bottom"><span>${icons.compass} Bez žurbe. Svaki pokušaj te vodi bliže.</span><button data-cmd="sources">Obuhvat i izvori ↗</button></div>
        <div class="snd-error" role="alert" ${this.error ? '' : 'hidden'}>${esc(this.error)}${this.expired ? '<button data-cmd="newday">Otvori današnji izazov →</button>' : ''}</div>
        ${this.canSave === false ? '<p class="snd-storage-note">Pregledač ne dozvoljava čuvanje podataka. Partija radi, ali nastavak i statistika neće biti sačuvani.</p>' : ''}
        <div class="snd-modal-host"></div><div class="snd-announcer" aria-live="polite" aria-atomic="true"></div>`;
      this.bind();
      if (this.busy) this.setBusy(true);
    }
    visual() {
      const s = this.state, q = s.question;
      let content, label, note;
      if (s.stage === 'quiz' && q.image) {
        content = `<div class="snd-photo-wrap"><img class="snd-landmark-photo" src="${safeUrl(q.image)}" alt="Znamenitost koju treba prepoznati" loading="eager"><div class="snd-photo-fallback" hidden>Fotografija nije dostupna. Možeš da preskočiš pitanje.</div></div><div class="snd-photo-credit">Foto: ${esc(q.credit)} · <a href="${safeUrl(licenseUrl(q.license) || q.image_source)}" target="_blank" rel="noopener noreferrer">${esc(q.license)}</a> · <a href="${safeUrl(q.image_source)}" target="_blank" rel="noopener noreferrer">Izvor ↗</a></div>`;
        label = 'RAZGLEDNICA IZ OVOG KRAJA'; note = 'Prepoznaješ li ovo mesto?';
      } else if (s.stage === 'quiz' && q.type === 'population') {
        content = `<div class="snd-pop-art"><div>${Array.from({length:28}, (_, i) => `<svg viewBox="0 0 20 42" aria-hidden="true" style="opacity:${[.25,.45,.7,1][i % 4]}"><circle cx="10" cy="6" r="4"/><path d="M5 14h10l2 14h-4v12H7V28H3Z"/></svg>`).join('')}</div><span>Jedan kraj. Mnogo priča.</span></div>`;
        label = 'LJUDI ČINE MESTO'; note = 'Razmišljaj o celoj teritoriji, ne samo o gradu.';
      } else if (s.stage === 'guess') {
        content = `<svg class="snd-shape" viewBox="0 0 360 360" role="img" aria-label="Obris nepoznate teritorije, sever je gore"><path d="${esc(s.shape)}"/></svg>`;
        label = 'JEDAN OBRIS. KOJI KRAJ?'; note = 'Oblik je uvek okrenut severom nagore.';
      } else {
        const last = [...s.guesses].reverse().find(g => !g.correct);
        content = `<svg class="snd-context-map" viewBox="0 0 600 800" role="img" aria-label="Položaj teritorije ${esc(s.territory.name)} na mapi"><g class="snd-map-base">${this.outline()}</g>${(s.map || []).map(t => `<path class="${t.id === s.territory.id ? 'snd-map-target' : last && t.id === last.id ? 'snd-map-last' : 'snd-map-cell'}" d="${esc(t.map_path)}"/>`).join('')}</svg><div class="snd-map-caption"><span></span>${esc(s.territory.name)}${last ? '<span class="snd-last-dot"></span>Poslednji promašaj' : ''}</div>`;
        label = s.stage === 'complete' ? 'JOŠ JEDAN KRAJ JE NA TVOJOJ MAPI' : 'SADA ZNAŠ GDE JE'; note = esc(s.territory.region);
      }
      return `<div class="snd-visual ${q && q.image && s.stage === 'quiz' ? 'snd-visual-photo' : ''}"><div class="snd-visual-top"><span>${label}</span><span class="snd-north">N <span>↑</span></span></div><div class="snd-art">${content}</div><div class="snd-visual-note"><span>✧</span>${note}</div><span class="snd-coordinate">SEVER JE GORE &nbsp; · &nbsp; MAPA ZA IGRU</span></div>`;
    }
    panel() {
      const s = this.state;
      if (s.stage === 'guess') return this.guessPanel();
      if (s.stage === 'reveal') return `<div class="snd-kicker">${s.solved ? 'DOBRO PRATIŠ TRAGOVE' : 'UPOZNAJ OVAJ KRAJ'}</div><h2>${s.solved ? 'To je to!' : 'Ovog puta je to…'}</h2><div class="snd-territory-name">${esc(s.territory.name)}</div><p class="snd-panel-copy">${s.solved ? `Teritorija je pogođena iz ${s.guesses.length}. pokušaja.` : 'Svaka partija je prilika da nešto novo zapamtiš.'} Hajde da je upoznamo malo bolje.</p>${this.history()}<button class="snd-primary" data-cmd="next">Upoznaj ovaj kraj ${icons.arrow}</button><button class="snd-text-button" data-cmd="finish">Završi bez dodatnih pitanja</button>`;
      if (s.stage === 'quiz') return this.quizPanel();
      return this.resultPanel();
    }
    guessPanel() {
      const s = this.state;
      return `<div class="snd-kicker">01 / PREPOZNAJ TERITORIJU</div><h2>Koji je ovo ${s.kind === 'district' ? 'kraj' : 'grad ili opština'}?</h2><p class="snd-panel-copy">${s.kind === 'district' ? 'Prepoznaj okrug ili Grad Beograd' : 'Prepoznaj opštinu ili grad'} po obliku. Svaki promašaj donosi novi trag.</p><div class="snd-attempts" aria-label="Iskorišćeno ${s.guesses.length} od ${s.max_guesses} pokušaja">${Array.from({length:s.max_guesses}, (_, i) => `<span class="${i < s.guesses.length ? 'is-used' : i === s.guesses.length ? 'is-current' : ''}">${i < s.guesses.length ? '↗' : i + 1}</span>`).join('')}<small>${s.max_guesses - s.guesses.length} preostalih</small></div><form class="snd-guess-form"><label class="snd-field-label" for="${this.root.id}-search">Tvoj odgovor</label><div class="snd-search-wrap">${icons.search}<input id="${this.root.id}-search" type="text" autocomplete="off" autocapitalize="off" spellcheck="false" placeholder="Počni da kucaš naziv…" role="combobox" aria-expanded="false" aria-autocomplete="list" aria-controls="${this.root.id}-suggestions"><div id="${this.root.id}-suggestions" class="snd-suggestions" role="listbox" aria-label="Predložene teritorije" hidden></div></div><p class="snd-search-note">Latinica, ćirilica ili bez kvačica — sve prihvatamo.</p><button class="snd-primary" type="submit" data-cmd="guess" disabled>Potvrdi odgovor ${icons.arrow}</button></form>${this.history()}${s.hint ? `<div class="snd-hint">✧ ${esc(s.hint_text)}</div>` : `<button class="snd-hint-button" data-cmd="hint" ${s.guesses.length < 3 ? 'disabled' : ''}>✧ ${s.guesses.length < 3 ? 'Pomoć se otključava posle 3 pokušaja' : 'Otkrij širi položaj'}</button>`}<button class="snd-text-button" data-cmd="giveup">Otkrij teritoriju i nastavi</button><div class="snd-trace-note">Kilometri i strelica vode <strong>od tvog odgovora ka traženoj teritoriji</strong>. Merimo vazdušno rastojanje između centara.</div>`;
    }
    history() {
      const s = this.state;
      if (!s.guesses.length) return '';
      return `<ol class="snd-history" aria-label="Prethodni pokušaji">${s.guesses.map((g, i) => `<li class="${g.correct ? 'is-correct' : ''}"><span class="snd-guess-number">${i + 1}</span><strong>${esc(g.name)}</strong>${g.correct ? '<span class="snd-distance">Pogođeno ✓</span>' : `<span class="snd-distance">${g.km === 0 ? '< 1' : g.km} km</span><span class="snd-direction" title="${esc(g.direction.name)}"><span>${g.direction.arrow}</span><small>${esc(g.direction.name)}</small></span>`}</li>`).join('')}</ol>`;
    }
    quizPanel() {
      const s = this.state, q = s.question, a = q.answer;
      return `<div class="snd-kicker">${String(s.question_index + 2).padStart(2,'0')} / ${esc({population:'STANOVNIŠTVO',region:'ŠIRI POLOŽAJ',orientation:'ORIJENTACIJA',membership:'OPŠTINE OKRUGA',seat:'SEDIŠTE OKRUGA',district:'PRIPADNOST OKRUGU',river:'REKE',landmark:'ZNAMENITOST',nearby:'PROSTORNI IZAZOV'}[q.type] || 'ISTRAŽIVANJE')}</div><span class="snd-territory-tag">${icons.pin}${esc(s.territory.name)}</span><h2 class="snd-question-title">${esc(q.title)}</h2>${q.subtitle ? `<p class="snd-panel-copy">${esc(q.subtitle)}</p>` : ''}<div class="snd-options">${q.options.map((o, i) => `<button class="snd-option ${a ? o.id === q.correct ? 'is-correct' : o.id === a.choice ? 'is-wrong' : 'is-muted' : ''}" data-choice="${esc(o.id)}" ${a ? 'disabled' : ''}><span>${String.fromCharCode(65+i)}</span><strong>${esc(o.label)}</strong>${a && o.id === q.correct ? '<b>✓</b>' : a && o.id === a.choice ? '<b>×</b>' : ''}</button>`).join('')}</div>${a ? `<div class="snd-explanation ${a.correct ? 'is-correct' : ''}" role="status"><strong>${a.skipped ? 'Sada znaš i ovo.' : a.correct ? 'Tako je!' : 'Još jedna nova stvar za pamćenje.'}</strong><p>${esc(q.explanation)}</p>${sourceLink(q.source)}</div><button class="snd-primary" data-cmd="next">${s.question_index + 1 === s.question_count ? 'Pogledaj rezultat' : 'Sledeća runda'} ${icons.arrow}</button>` : '<button class="snd-text-button" data-cmd="skip">Ne znam, pokaži odgovor</button>'}`;
    }
    record() {
      const s = this.state;
      const all = storage.get('stats', { district: { days:[], discovered:[] }, municipality: { days:[], discovered:[] } });
      const stats = all[s.kind] || { days:[], discovered:[] };
      if (!stats.discovered.includes(s.territory.id)) stats.discovered.push(s.territory.id);
      if (s.mode === 'daily' && !stats.days.includes(s.date)) stats.days.push(s.date);
      stats.days = stats.days.sort().slice(-400); all[s.kind] = stats; storage.set('stats', all);
    }
    streak() {
      const stats = storage.get('stats', {})[this.kind] || { days:[] };
      let day = this.info.date, count = 0;
      if (!stats.days.includes(day)) day = this.previousDay(day);
      while (stats.days.includes(day)) { count++; day = this.previousDay(day); }
      return count;
    }
    previousDay(day) { const d = new Date(day + 'T12:00:00Z'); d.setUTCDate(d.getUTCDate() - 1); return d.toISOString().slice(0,10); }
    resultPanel() {
      const s = this.state, stats = storage.get('stats', {})[s.kind] || { discovered:[] };
      return `<div class="snd-kicker">MALA AVANTURA, NOVO ZNANJE</div><h2>Ovaj kraj je sada<br><em>na tvom dlanu.</em></h2><div class="snd-territory-name snd-result-name">${esc(s.territory.name)}</div><div class="snd-score-grid"><div><span>TERITORIJA</span><strong>${s.solved ? s.guesses.length + '<small>/6</small>' : '—'}</strong><p>${s.solved ? 'pokušaja do odgovora' : 'odgovor je otkriven'}</p></div><div><span>DODATNA PITANJA</span><strong>${s.score}<small>/${s.question_count}</small></strong><p>tačnih odgovora</p></div></div><div class="snd-result-meta"><span>${s.hint ? '✧ Uz pomoć' : '✓ Bez pomoći'}</span><span>↗ Upoznato ${stats.discovered.length}/${this.info.counts[s.kind]}</span>${s.mode === 'daily' ? `<span>♧ Niz: ${this.streak()} dana</span>` : ''}</div><button class="snd-primary" data-cmd="share">Podeli rezultat ${icons.arrow}</button><button class="snd-secondary" data-cmd="again">${s.mode === 'daily' ? 'Nastavi u režimu vežbanja' : 'Istraži sledeću teritoriju'}</button>${s.mode === 'daily' ? '<p class="snd-next-day">Novi izazov stiže u ponoć po vremenu u Srbiji.</p>' : ''}<details class="snd-review"><summary>Šta smo danas saznali?</summary>${s.review.map(r => `<div><strong>${r.answer.correct ? '✓' : r.answer.skipped ? '↳' : '×'} ${esc(r.correct_label)}</strong><p>${esc(r.explanation)}</p>${sourceLink(r.source)}</div>`).join('')}</details>`;
    }
    bind() {
      this.root.querySelectorAll('[data-cmd]').forEach(b => b.addEventListener('click', async event => {
        const command = b.dataset.cmd;
        if (command === 'guess') return;
        event.preventDefault(); if (this.busy) return;
        if (['district','municipality'].includes(command)) return this.switchGame(command, this.mode);
        if (['daily','practice'].includes(command)) return this.switchGame(this.kind, command);
        if (command === 'again') { this.mode = 'practice'; return this.start(true); }
        if (command === 'newday') { this.expired = false; this.state = null; return this.boot(); }
        if (command === 'help') return this.help();
        if (command === 'sources') return this.sources();
        if (command === 'share') return this.share();
        return this.action(command);
      }));
      this.root.querySelectorAll('[data-choice]').forEach(b => b.addEventListener('click', () => this.action('answer', b.dataset.choice)));
      const image = this.root.querySelector('.snd-landmark-photo');
      if (image) image.addEventListener('error', () => { image.hidden = true; this.root.querySelector('.snd-photo-fallback').hidden = false; });
      const form = this.root.querySelector('.snd-guess-form');
      if (form) {
        const input = form.querySelector('input'), list = form.querySelector('[role=listbox]'), submit = form.querySelector('button');
        let matches = [], active = -1;
        const close = () => { list.hidden = true; input.setAttribute('aria-expanded','false'); input.removeAttribute('aria-activedescendant'); active = -1; };
        const choose = index => { if (!matches[index]) return; this.selected = matches[index]; input.value = this.selected.name; submit.disabled = false; close(); input.focus(); };
        const display = () => {
          const query = normalize(input.value); const guessed = new Set(this.state.guesses.map(g => g.id));
          matches = this.state.catalog.filter(t => !guessed.has(t.id) && (!query || [t.name,...(t.aliases || [])].some(n => normalize(n).includes(query)))).sort((a,b) => a.name.localeCompare(b.name,'sr-Latn'));
          active = -1; input.removeAttribute('aria-activedescendant');
          list.innerHTML = matches.length ? matches.map((t,i) => `<div role="option" id="${this.root.id}-option-${i}" aria-selected="false" data-index="${i}">${esc(t.name)}<span>↵</span></div>`).join('') : '<div class="snd-no-matches">Nema rezultata. Probaj drugi naziv.</div>';
          list.hidden = false; input.setAttribute('aria-expanded','true');
          list.querySelectorAll('[role=option]').forEach(o => o.addEventListener('pointerdown', event => { event.preventDefault(); choose(Number(o.dataset.index)); }));
        };
        input.addEventListener('focus', display);
        input.addEventListener('input', () => { this.selected = null; submit.disabled = true; display(); });
        input.addEventListener('blur', () => setTimeout(close,150));
        input.addEventListener('keydown', event => {
          if (['ArrowDown','ArrowUp'].includes(event.key)) {
            event.preventDefault(); if (list.hidden) display(); if (!matches.length) return;
            active = (active + (event.key === 'ArrowDown' ? 1 : -1) + matches.length) % matches.length;
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
    modal(title, content) {
      const host = this.root.querySelector('.snd-modal-host'), before = document.activeElement;
      host.innerHTML = `<div class="snd-modal-backdrop"><section class="snd-modal" role="dialog" aria-modal="true" aria-labelledby="${this.root.id}-modal-title" tabindex="-1"><button class="snd-modal-close" aria-label="Zatvori">×</button><div class="snd-kicker">SRBIJA NA DLANU</div><h2 id="${this.root.id}-modal-title">${esc(title)}</h2>${content}</section></div>`;
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
      this.modal('Prati tragove. Upoznaj kraj.', '<ol class="snd-help-list"><li><strong>Prepoznaj obris.</strong> Imaš šest pokušaja. Izaberi naziv iz pretrage i potvrdi odgovor.</li><li><strong>Prati smer i kilometre.</strong> Strelica vodi od centra izabrane ka centru tražene teritorije. Rastojanje je vazdušno, a sever je uvek gore.</li><li><strong>Zatraži pomoć.</strong> Posle trećeg promašaja možeš da otkriješ širi položaj. Pomoć je vidljiva u rezultatu.</li><li><strong>Upoznaj teritoriju.</strong> Posle otkrivanja slede tri kratka pitanja. Nastavljaš i ako nisi pogodio/la obris.</li></ol><p>Dnevni izazov je isti za sve igrače i menja se u ponoć po vremenu u Srbiji. Vežbanje daje nove nasumične partije.</p><p>Rezultati, kolekcija i niz završenih dnevnih partija čuvaju se u ovom pregledaču, odvojeno za obe igre. Deljeni rezultat ne otkriva rešenje.</p>');
    }
    sources() { this.modal('Mapa, podaci i fotografije', `<p>${esc(this.info.coverage)}</p><p>Obrisi su pojednostavljeni radi prikaza. Udaljenost se računa između geometrijskih centara, ne između granica ili administrativnih sedišta.</p><ul>${this.info.sources.map(s => `<li>${sourceLink(s.url,s.label)}<br><small>${esc(s.license)}</small></li>`).join('')}</ul><p>Uz odgovor na pitanje prikazan je njegov izvor. Fotografije imaju navedene autore i licence; kada fotografija nije dostupna, poslednja runda je prostorni izazov.</p>`); }
    async share() {
      const s = this.state;
      const marks = s.guesses.map(g => g.correct ? '🟩' : '⬜').join('') || '↳';
      const review = s.review.map(r => r.answer.correct ? '🟩' : r.answer.skipped ? '⬜' : '🟨').join('');
      const text = `Srbija na dlanu · ${s.kind === 'district' ? 'Okruzi' : 'Opštine i gradovi'}\n${s.mode === 'daily' ? `Dnevni izazov #${s.number} · ${s.date}` : 'Vežbanje'}\n${marks} ${s.solved ? s.guesses.length + '/6' : 'Otkriveno'}\nKviz ${review} ${s.score}/${s.question_count}\n${s.hint ? '✧ Uz pomoć' : '✓ Bez pomoći'}\n${location.href.split('#')[0]}`;
      try { await navigator.clipboard.writeText(text); this.modal('Rezultat je kopiran!', '<p>Pošalji ga prijateljima i uporedite kako ste prošli. Rešenje je sačuvano za njihovu partiju.</p>'); }
      catch (_) { this.modal('Podeli svoju malu avanturu', `<p>Kopiraj tekst i pošalji prijateljima:</p><textarea class="snd-share-text" readonly rows="8" aria-label="Rezultat za kopiranje">${esc(text)}</textarea>`); const area = this.root.querySelector('textarea'); area.focus(); area.select(); }
    }
  }
  document.querySelectorAll('.snd-game[data-api]').forEach(root => new Game(root));
  // Pure helpers are also available to automated checks without a browser.
  if (typeof module !== 'undefined' && module.exports) module.exports = { normalize };
})();
