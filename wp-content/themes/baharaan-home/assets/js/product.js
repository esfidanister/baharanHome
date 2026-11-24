document.addEventListener('DOMContentLoaded',()=>{
 const buttons=[...document.querySelectorAll('.bhp-thumbs button[data-image]')];
 if(buttons.length) buttons.forEach((button,index)=>{
  button.type='button';
  button.setAttribute('aria-label',`نمایش تصویر ${index+1}`);
  button.setAttribute('aria-pressed',button.classList.contains('is-active')?'true':'false');
  button.addEventListener('click',()=>{
   buttons.forEach(item=>{const active=item===button;item.setAttribute('aria-pressed',active?'true':'false');item.classList.toggle('is-active',active)});
   const main=document.querySelector('#bhp-main-image img'),image=button.dataset.image;
   if(!main||!image)return;
   const preload=new Image();preload.onload=()=>{main.src=image;main.removeAttribute('srcset');main.alt=button.querySelector('img')?.alt||main.alt||''};preload.src=image;
  });
  button.addEventListener('keydown',event=>{
   if(event.key!=='ArrowLeft'&&event.key!=='ArrowRight')return;
   event.preventDefault();
   const direction=event.key==='ArrowRight'?-1:1;
   buttons[(index+direction+buttons.length)%buttons.length].focus();
  });
 });
 const sticky=document.querySelector('.bhp-sticky-buy');
 const cartBox=document.querySelector('#bhp-cart');
 if(sticky&&cartBox&&'IntersectionObserver' in window){
  const watch=new IntersectionObserver(([entry])=>sticky.classList.toggle('is-on',!entry.isIntersecting),{threshold:.35});
  watch.observe(cartBox);
 }
 const form=document.querySelector('.bhp-cart-form form.cart');
 if(form)form.setAttribute('aria-label','افزودن محصول به سبد خرید');
 const main=document.querySelector('#bhp-main-image'),mainImg=main?.querySelector('img');
 if(main&&mainImg){const zoom=document.createElement('button');zoom.type='button';zoom.className='bhp-zoom-trigger';zoom.setAttribute('aria-label','بزرگ‌نمایی تصویر محصول');zoom.textContent='بزرگ';main.appendChild(zoom);let opener=null;let previousOverflow='';let activeKeydown=null;const closeZoom=()=>{const box=document.querySelector('.bhp-lightbox');if(!box)return;box.remove();if(activeKeydown)document.removeEventListener('keydown',activeKeydown);document.body.style.overflow=previousOverflow;opener?.focus();activeKeydown=null};const openZoom=()=>{opener=document.activeElement;previousOverflow=document.body.style.overflow;document.body.style.overflow='hidden';const box=document.createElement('div');box.className='bhp-lightbox';box.setAttribute('role','dialog');box.setAttribute('aria-modal','true');box.setAttribute('aria-label','نمایش بزرگ تصویر محصول');const closeButton=document.createElement('button');closeButton.type='button';closeButton.setAttribute('aria-label','بستن');closeButton.textContent='×';const image=document.createElement('img');image.src=mainImg.currentSrc||mainImg.src;image.alt=mainImg.alt||'';box.append(closeButton,image);document.body.appendChild(box);const focusables=[closeButton];activeKeydown=e=>{if(e.key==='Escape'){closeZoom();return}if(e.key!=='Tab')return;e.preventDefault();const next=e.shiftKey?focusables.length-1:0;focusables[next].focus()};document.addEventListener('keydown',activeKeydown);box.addEventListener('click',e=>{if(e.target===box||e.target===closeButton)closeZoom()});closeButton.focus()};zoom.addEventListener('click',openZoom)}
});
