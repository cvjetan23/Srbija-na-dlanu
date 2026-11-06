(function(root,factory){const api=factory();if(typeof module==='object'&&module.exports)module.exports=api;else root.SND_L10N=api;})(typeof globalThis!=='undefined'?globalThis:this,function(){
  'use strict';
  let config=typeof globalThis.SND_CONFIG==='object'?globalThis.SND_CONFIG:{};
  const esc=s=>String(s).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  const decode=s=>String(s).replace(/&(amp|quot|apos|lt|gt|nbsp);|&#(\d+);|&#x([0-9a-f]+);/gi,(whole,name,n,h)=>name?({amp:'&',quot:'"',apos:"'",lt:'<',gt:'>',nbsp:'\u00a0'}[name.toLowerCase()]||whole):String.fromCodePoint(parseInt(n||h,n?10:16)));
  const regex=s=>s.replace(/[.*+?^${}()|[\]\\]/g,'\\$&');
  let patterns=null;
  const api={
    config(){return config;},
    configure(value){config={...config,...value};patterns=null;},
    t(source,vars={}){let value=(config.dictionary||{})[source]||source;for(const [key,text] of Object.entries(vars))value=value.split('{'+key+'}').join(String(text));return value;},
    plural(n){n=Math.abs(Math.trunc(Number(n)||0));if(['sr','ru'].includes(config.language||'sr')){if(n%10===1&&n%100!==11)return 'one';if(n%10>=2&&n%10<=4&&!(n%100>=12&&n%100<=14))return 'few';return 'many';}return n===1?'one':'many';},
    days(n){n=Math.max(0,Math.trunc(Number(n)||0));const category=api.plural(n),key='@days_'+category;
      const fallback={sr:{one:'dan',few:'dana',many:'dana'},en:{one:'day',many:'days'},de:{one:'Tag',many:'Tage'},ru:{one:'день',few:'дня',many:'дней'}};
      if((config.dictionary||{})[key])return api.t(key,{count:n});const lang=fallback[config.language]||fallback.sr;return n+' '+(lang[category]||lang.many);
    },
    text(value){
      value=String(value);const dict=config.dictionary||{};if(Object.prototype.hasOwnProperty.call(dict,value))return dict[value];
      const day=/^(\d+) dana?$/.exec(value);if(day)return api.days(day[1]);
      if(patterns===null){patterns=[];for(const source of Object.keys(dict)){if(source[0]==='@'||!source.includes('{'))continue;const names=[...source.matchAll(/\{([a-z_]+)\}/g)].map(m=>m[1]);if(!names.length)continue;let pattern=regex(source);for(const name of names)pattern=pattern.replace(regex('{'+name+'}'),'(.+?)');patterns.push({source,names,pattern:new RegExp('^'+pattern+'$','u')});}patterns.sort((a,b)=>b.source.length-a.source.length);}
      for(const item of patterns){const match=item.pattern.exec(value);if(!match)continue;const vars={};item.names.forEach((key,i)=>{let v=match[i+1];if(key==='days'&&/^(\d+) dana?$/.test(v))v=api.days(parseInt(v,10));else v=dict[v]||v;vars[key]=v;});return api.t(item.source,vars);}
      return value;
    },
    html(markup){
      return String(markup).replace(/<(script|style)\b[^>]*>[\s\S]*?<\/\1>|<([a-z][a-z0-9]*)\b[^>]*\bdata-snd-original(?:="[^"]*")?[^>]*>[\s\S]*?<\/\2>|<[^>]*>|[^<]+/gi,part=>{
        if(/^<(script|style)\b/i.test(part)||part.includes('data-snd-original'))return part;
        if(part[0]==='<')return part.replace(/\b(aria-label|title|placeholder|alt)="([^"]*)"/g,(whole,key,raw)=>{const original=decode(raw),value=api.text(original);return value===original?whole:key+'="'+esc(value)+'"';});
        const original=decode(part),trimmed=original.trim();if(!trimmed)return part;const value=api.text(trimmed);if(value===trimmed)return part;const spaces=/^(\s*)[\s\S]*?(\s*)$/.exec(original);return spaces[1]+esc(value)+spaces[2];
      });
    }
  };
  return api;
});
