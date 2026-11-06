(function () {
  'use strict';
  document.querySelectorAll('[data-snd-atlas]').forEach(atlas=>{
    const svg=atlas.querySelector('[data-map-fit]'),shapes=atlas.querySelector('[data-map-shapes]'),name=atlas.querySelector('[data-map-name]');
    const initial=name.textContent,regions=[...atlas.querySelectorAll('[data-territory]')];
    // getBBox also supports imported curved/relative paths; the built-in maps
    // are already fitted server-side, so links remain useful without scripts.
    try{const b=shapes.getBBox(),pad=Math.max(b.width,b.height)*.07;if(b.width>0&&b.height>0)svg.setAttribute('viewBox',[b.x-pad,b.y-pad,b.width+2*pad,b.height+2*pad].join(' '));}catch(_){}
    const reset=()=>{name.textContent=initial;regions.forEach(region=>region.classList.remove('is-linked'));};
    const show=region=>{reset();name.textContent=region.dataset.name;region.classList.add('is-linked');};
    regions.forEach(region=>{
      region.addEventListener('pointerenter',()=>show(region));region.addEventListener('focus',()=>show(region));
      region.addEventListener('pointerleave',()=>{if(document.activeElement!==region)reset();});region.addEventListener('blur',reset);
    });
    document.querySelectorAll('[data-atlas-item]').forEach(item=>{
      const region=regions.find(r=>r.dataset.territory===item.dataset.atlasItem);if(!region)return;
      item.addEventListener('pointerenter',()=>show(region));item.addEventListener('focus',()=>show(region));item.addEventListener('pointerleave',reset);item.addEventListener('blur',reset);
    });
  });
  const field=document.getElementById('snd-library-search');if(!field)return;
  const alphabet={а:'a',б:'b',в:'v',г:'g',д:'d',ђ:'dj',е:'e',ж:'z',з:'z',и:'i',ј:'j',к:'k',л:'l',љ:'lj',м:'m',н:'n',њ:'nj',о:'o',п:'p',р:'r',с:'s',т:'t',ћ:'c',у:'u',ф:'f',х:'h',ц:'c',ч:'c',џ:'dz',ш:'s'};
  const key=value=>value.toLowerCase().replace(/[а-яђјљњћџ]/g,c=>alphabet[c]||c).replace(/đ/g,'dj').normalize('NFD').replace(/[\u0300-\u036f]/g,'').replace(/[^a-z0-9]+/g,'-').replace(/^-+|-+$/g,'');
  const cards=[...document.querySelectorAll('.snd-district-card')],empty=document.querySelector('.snd-library-empty');
  const filter=()=>{const query=key(field.value);let count=0;cards.forEach(card=>{card.hidden=!key(card.dataset.search).includes(query);if(!card.hidden)count++;});if(empty)empty.hidden=count>0;};
  field.addEventListener('input',filter);field.form.addEventListener('submit',event=>{event.preventDefault();filter();});
})();
