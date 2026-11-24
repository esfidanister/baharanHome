document.addEventListener('DOMContentLoaded',()=>{
 document.querySelectorAll('.bhp-filters form').forEach(form=>{
  const min=form.querySelector('[name="min_price"]'),max=form.querySelector('[name="max_price"]');
  if(!min||!max)return;
  const filters=form.closest('.bhp-filters');
  if(filters){
   const syncAccessibility=()=>filters.setAttribute('aria-hidden',String(!filters.classList.contains('is-open')&&window.matchMedia('(max-width:1024px)').matches));
   syncAccessibility();
   new MutationObserver(syncAccessibility).observe(filters,{attributes:true,attributeFilter:['class']});
  }
  let error=form.querySelector('.bhp-filter-error');
  const clear=()=>{error?.remove();min.removeAttribute('aria-invalid');max.removeAttribute('aria-invalid')};
  form.addEventListener('submit',event=>{
   clear();
   const minValue=min.value===''?null:Number(min.value),maxValue=max.value===''?null:Number(max.value);
   if(minValue!==null&&maxValue!==null&&Number.isFinite(minValue)&&Number.isFinite(maxValue)&&minValue>maxValue){
    event.preventDefault();
    error=document.createElement('p');
    error.className='bhp-filter-error';
    error.setAttribute('role','alert');
    error.textContent='حداقل قیمت باید کمتر یا مساوی حداکثر قیمت باشد.';
    max.insertAdjacentElement('afterend',error);
    min.setAttribute('aria-invalid','true');max.setAttribute('aria-invalid','true');min.focus();
   }else{
    if(minValue===null)min.disabled=true;
    if(maxValue===null)max.disabled=true;
   }
  });
 });
});
