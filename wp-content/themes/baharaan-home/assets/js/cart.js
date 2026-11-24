document.addEventListener('DOMContentLoaded',function(){
  var form=document.querySelector('.woocommerce-cart-form');
  if(!form)return;
  var updateTimer=null,submitting=false;
  var updateButton=form.querySelector('button[name="update_cart"],input[name="update_cart"]');
  function setBusy(state){
    form.classList.toggle('is-cart-updating',state);
    form.querySelectorAll('.bh-qty-minus,.bh-qty-plus,.qty').forEach(function(el){el.disabled=state;});
  }
  function requestUpdate(){
    if(submitting||!updateButton)return;
    submitting=true;
    setBusy(true);
    updateButton.disabled=false;
    if(typeof form.requestSubmit==='function'){
      form.requestSubmit(updateButton);
    }else{
      updateButton.click();
    }
  }
  function scheduleUpdate(){
    if(submitting||!updateButton)return;
    window.clearTimeout(updateTimer);
    updateTimer=window.setTimeout(requestUpdate,180);
  }
  form.querySelectorAll('.quantity').forEach(function(box){
    var input=box.querySelector('.qty');
    if(!input)return;
    var minus=document.createElement('button'),plus=document.createElement('button');
    minus.type='button';minus.className='bh-qty-minus';minus.setAttribute('aria-label','کاهش تعداد');minus.textContent='−';
    plus.type='button';plus.className='bh-qty-plus';plus.setAttribute('aria-label','افزایش تعداد');plus.textContent='+';
    if(!box.querySelector('.bh-qty-minus')){
      box.insertBefore(minus,input);box.appendChild(plus);
      function update(delta){
        var min=parseFloat(input.min)||1,max=parseFloat(input.max)||Infinity,step=parseFloat(input.step)||1,value=parseFloat(input.value)||min;
        input.value=Math.min(max,Math.max(min,value+delta*step));
        input.dispatchEvent(new Event('change',{bubbles:true}));
      }
      minus.addEventListener('click',function(){update(-1)});plus.addEventListener('click',function(){update(1)});
    }
    input.addEventListener('input',scheduleUpdate);
    input.addEventListener('change',scheduleUpdate);
  });
  form.addEventListener('submit',function(){submitting=true;setBusy(true);});
});
