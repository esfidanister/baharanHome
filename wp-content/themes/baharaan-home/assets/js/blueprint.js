document.addEventListener('DOMContentLoaded',()=>{
 const reduce=window.matchMedia('(prefers-reduced-motion: reduce)').matches||document.body.classList.contains('bhb-motion-off');
 const mega=document.querySelector('.bhp-mega');
 if(mega){
  mega.querySelectorAll('[data-mega]').forEach(link=>{
   const activate=()=>{
    const id=link.dataset.mega;
    mega.querySelectorAll('[data-mega]').forEach(item=>item.classList.toggle('is-current',item===link));
    mega.querySelectorAll('[data-mega-panel]').forEach(panel=>{panel.hidden=panel.dataset.megaPanel!==id;});
   };
   link.addEventListener('mouseenter',activate);
   link.addEventListener('focus',activate);
  });
  mega.querySelector('summary')?.addEventListener('keydown',event=>{
   if(event.key==='ArrowDown'){mega.open=true;mega.querySelector('[data-mega]')?.focus();event.preventDefault();}
  });
 }
 document.querySelectorAll('[data-bhb-hero]').forEach(hero=>{
  const slides=[...hero.querySelectorAll('[data-slide]')];
  const dots=[...hero.querySelectorAll('[data-slide-to]')];
  if(slides.length<2) return;
  const show=index=>{
   slides.forEach((slide,i)=>{slide.hidden=i!==index;slide.classList.toggle('is-on',i===index);});
   dots.forEach((dot,i)=>dot.toggleAttribute('aria-current',i===index));
  };
  show(Math.max(0,slides.findIndex(slide=>!slide.hidden)));
  dots.forEach(dot=>dot.addEventListener('click',()=>show(Number(dot.dataset.slideTo)||0)));
  hero.querySelector('[data-slide-next]')?.addEventListener('click',()=>{
   const current=Math.max(0,slides.findIndex(slide=>!slide.hidden));
   show((current+1)%slides.length);
  });
 });
 document.querySelectorAll('[data-bhb-rail]').forEach(section=>{
  const track=section.querySelector('.bhb-rail-track');
  if(!track) return;
  const slide=forward=>{
   const before=track.scrollLeft;
   const rtl=getComputedStyle(track).direction==='rtl';
   const amount=Math.max(track.clientWidth*.72,240)*(forward?1:-1)*(rtl?-1:1);
   track.scrollBy({left:amount,behavior:reduce?'auto':'smooth'});
   window.setTimeout(()=>{if(track.scrollLeft===before) track.scrollTo({left:0,behavior:reduce?'auto':'smooth'});},480);
  };
  section.querySelectorAll('[data-rail]').forEach(button=>{
   button.addEventListener('click',()=>slide(button.dataset.rail==='next'));
  });
  if(!reduce && section.hasAttribute('data-bhb-auto')){
   let timer=window.setInterval(()=>slide(true),3400);
   const pause=()=>window.clearInterval(timer);
   const play=()=>{pause();timer=window.setInterval(()=>slide(true),3400);};
   section.addEventListener('mouseenter',pause);
   section.addEventListener('mouseleave',play);
   section.addEventListener('focusin',pause);
   section.addEventListener('focusout',play);
  }
 });
 const brandStrip=document.querySelector('[data-bhb-brand-strip]');
 if(brandStrip&&!reduce){
  let timer;
  const stop=()=>{if(timer) window.clearInterval(timer);};
  const play=()=>{stop();timer=window.setInterval(()=>{
   const max=Math.max(0,brandStrip.scrollWidth-brandStrip.clientWidth);
   if(brandStrip.scrollLeft>=max-4) brandStrip.scrollTo({left:0,behavior:'smooth'});
   else brandStrip.scrollBy({left:170,behavior:'smooth'});
  },3000);};
  brandStrip.addEventListener('mouseenter',stop);
  brandStrip.addEventListener('mouseleave',play);
  brandStrip.addEventListener('focusin',stop);
  brandStrip.addEventListener('focusout',play);
  brandStrip.addEventListener('touchstart',stop,{passive:true});
  brandStrip.addEventListener('touchend',()=>window.setTimeout(play,1800),{passive:true});
  play();
 }
 if(!reduce && 'IntersectionObserver' in window){
  const seen=new IntersectionObserver(entries=>{
   entries.forEach(entry=>{
    if(!entry.isIntersecting) return;
    entry.target.classList.add('is-in');
    seen.unobserve(entry.target);
   });
  },{threshold:.16});
  document.querySelectorAll('.bhb-section,.bhb-hero-stage,.bh-footer').forEach(node=>{
   node.classList.add('bhb-reveal','is-in');
   seen.observe(node);
  });
 }
});
