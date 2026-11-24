document.addEventListener('DOMContentLoaded',()=>{
 const nav=document.querySelector('.bh-nav'),mega=document.querySelector('.bhp-mega'),toggle=document.querySelector('.bh-menu-toggle'),drawer=document.querySelector('#bhb-drawer'),menu=document.querySelector('#bh-primary-menu');
 if(!nav)return;
 let lastFocus=null;
 const close=()=>{if(mega)mega.removeAttribute('open');if(toggle)toggle.setAttribute('aria-expanded','false');menu?.classList.remove('is-open');nav?.classList.remove('is-open');if(drawer){drawer.setAttribute('hidden','');drawer.setAttribute('aria-hidden','true')}document.body.classList.remove('bhb-nav-open','bhb-mega-open');if(lastFocus&&document.contains(lastFocus)){lastFocus.focus();lastFocus=null}};
 const positionMega=()=>{
  if(!mega?.open)return;
  const panel=mega.querySelector('.bhp-mega-panel');
  const summary=mega.querySelector(':scope>summary');
  if(!panel||!summary||window.innerWidth<701)return;
  const rect=summary.getBoundingClientRect();
  const top=Math.max(12,Math.round(rect.bottom+12));
  const width=Math.min(1040,Math.max(0,window.innerWidth-32));
  const side=Math.max(16,Math.round((window.innerWidth-width)/2));
  panel.style.setProperty('--bh-mega-top',`${top}px`);
  // Some legacy header rules create a positioned containing block for the
  // fixed panel. Set the intended viewport top first, then compensate for
  // that containing-block offset so the panel visually hugs the summary.
  panel.style.setProperty('top',`${top}px`,'important');
  // Keep the open panel viewport-bound even when legacy shop.css rules with
  // !important positioning are loaded before/after the visual refresh layer.
  panel.style.removeProperty('inset-inline-start');
  panel.style.removeProperty('inset-inline-end');
  panel.style.removeProperty('inset-inline');
  panel.style.setProperty('right','auto','important');
  panel.style.setProperty('left','0px','important');
  panel.style.setProperty('transform','none','important');
  panel.style.setProperty('width',`${width}px`,'important');
  panel.style.setProperty('max-width',`${width}px`,'important');
  panel.style.setProperty('margin-inline','0','important');
  const baseLeft=panel.getBoundingClientRect().left;
  panel.style.setProperty('left',`${Math.round(side-baseLeft)}px`,'important');
  const baseTop=panel.getBoundingClientRect().top;
  panel.style.setProperty('top',`${Math.round(top-(baseTop-top))}px`,'important');
 };
 const openDrawer=()=>{if(!drawer)return;lastFocus=document.activeElement;drawer.removeAttribute('hidden');drawer.setAttribute('aria-hidden','false');drawer.setAttribute('role','dialog');drawer.setAttribute('aria-modal','true');toggle?.setAttribute('aria-expanded','true');document.body.classList.add('bhb-nav-open');drawer.querySelector('.bhb-drawer-close')?.focus();};
 toggle?.addEventListener('click',()=>{if(drawer?.hasAttribute('hidden'))openDrawer();else close();});
 drawer?.setAttribute('aria-hidden','true');
 drawer?.querySelector('.bhb-drawer-close')?.addEventListener('click',close);
 mega?.addEventListener('toggle',()=>{
  document.body.classList.toggle('bhb-mega-open',!!mega.open);
  mega.querySelector(':scope>summary')?.setAttribute('aria-expanded',String(!!mega.open));
  positionMega();
 });
 window.addEventListener('resize',positionMega,{passive:true});
 window.addEventListener('scroll',positionMega,{passive:true});
 document.addEventListener('pointerdown',e=>{
  const insideMega=!!e.target.closest('.bhp-mega');
  const insideDrawer=!!e.target.closest('#bhb-drawer');
  const isMenuToggle=!!e.target.closest('.bh-menu-toggle');
  if((mega?.open&&!insideMega)||(!e.target.closest('.bh-nav')&&!insideDrawer&&!isMenuToggle))close();
 });
 document.addEventListener('keydown',e=>{if(e.defaultPrevented)return;if(e.key==='Escape'){close();toggle?.focus()}});
 drawer?.querySelectorAll('a').forEach(a=>a.addEventListener('click',close));
 const selectMega=(link)=>{
  if(!mega||!link)return;
  const id=link.getAttribute('data-mega');
  mega.querySelectorAll('[data-mega]').forEach(n=>n.classList.toggle('is-current',n===link));
  mega.querySelectorAll('[data-mega-panel]').forEach(panel=>{
   const active=panel.getAttribute('data-mega-panel')===id;
   panel.toggleAttribute('hidden',!active);
   panel.classList.toggle('is-mega-visible',active);
  });
 };
 mega?.querySelectorAll('[data-mega]').forEach(link=>{
  link.addEventListener('pointerenter',()=>selectMega(link));
  link.addEventListener('focus',()=>selectMega(link));
 });
 if(mega){
  mega.querySelector(':scope>summary')?.setAttribute('aria-expanded',String(!!mega.open));
  const first=mega.querySelector('[data-mega].is-current')||mega.querySelector('[data-mega]');
  if(first)selectMega(first);
 }
});
