document.addEventListener('DOMContentLoaded',()=>{
 const form=document.querySelector('.bh-search');if(!form)return;
 const input=form.querySelector('input[type="search"]'),box=form.querySelector('.bh-search-results');if(!input||!box)return;
 let timer=0,controller=null,active=-1;
 const hide=()=>{box.hidden=true;input.setAttribute('aria-expanded','false');active=-1};
 const node=(tag,text,cls)=>{const e=document.createElement(tag);if(cls)e.className=cls;e.textContent=text;return e};
 const showItems=(items)=>{box.replaceChildren();active=-1;if(!items.length){box.append(node('p','محصولی با این عبارت پیدا نشد.','bh-search-empty'));box.hidden=false;input.setAttribute('aria-expanded','true');return}
  items.forEach((item,i)=>{const a=document.createElement('a');a.className='bh-search-result';a.href=item.url;a.setAttribute('role','option');a.id=`bh-search-option-${i}`;const img=document.createElement('img');img.src=item.image;img.alt='';img.width=48;img.height=48;img.loading='lazy';const copy=document.createElement('span');copy.className='bh-search-result-copy';copy.append(node('b',item.title),node('small',item.price||item.stock||''));a.append(img,copy,node('i',item.stock||''));box.append(a)});
  box.hidden=false;input.setAttribute('aria-expanded','true');
 };
 input.addEventListener('input',()=>{clearTimeout(timer);const q=input.value.trim();if(q.length<(BHSearch.minimum||2)){hide();return}timer=setTimeout(async()=>{if(controller)controller.abort();controller=new AbortController();const params=new URLSearchParams({action:'bhc_live_search',nonce:BHSearch.nonce,q});try{const r=await fetch(`${BHSearch.ajax}?${params}`,{credentials:'same-origin',signal:controller.signal});const json=await r.json();if(json.success)showItems(json.data.items||[]);else hide()}catch(err){if(err.name!=='AbortError')hide()}},240)});
 input.addEventListener('keydown',e=>{const rows=[...box.querySelectorAll('.bh-search-result')];if(e.key==='Escape'){hide();return}if(!rows.length)return;if(e.key==='ArrowDown'||e.key==='ArrowUp'){e.preventDefault();active=(active+(e.key==='ArrowDown'?1:-1)+rows.length)%rows.length;rows.forEach((row,i)=>row.setAttribute('aria-selected',String(i===active)));input.setAttribute('aria-activedescendant',rows[active].id)}if(e.key==='Enter'&&active>=0){e.preventDefault();rows[active].click()}});
 document.addEventListener('click',e=>{if(!form.contains(e.target))hide()});form.addEventListener('submit',()=>hide());
});
