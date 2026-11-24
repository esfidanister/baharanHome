(function(){'use strict';document.addEventListener('DOMContentLoaded',function(){var root=document.querySelector('.bhc-admin');if(!root)return;var form=root.querySelector('form[action="options.php"]'),table=form&&form.querySelector('.form-table');if(!form||!table)return;var rows=[].slice.call(table.querySelectorAll('tr'));var headings={'تماس':'contacts','نماد اعتماد':'contacts','پیام آغازین چت':'contacts','دستیار هوش مصنوعی':'ai','سرویس هوش مصنوعی':'ai','آدرس API سازگار با OpenAI':'ai','کلید API':'ai','مدل‌های OpenRouter':'ai','تصویر':'media','عنوان صفحه نخست':'home','زیرعنوان صفحه نخست':'home','هویت هدر':'home','حرکت‌های رابط':'media','نوار اطلاع‌رسانی':'home','ساعت پشتیبانی':'home','بخش‌های صفحه‌ی خانه':'home','لوگوی فروشگاه':'home','کالای هیروی صفحه‌ی خانه':'home','دکمه‌ی اصلی صفحه خانه':'home','متن دکمه دوم':'home','متن معرفی فوتر':'home','اجزای محصول':'tools'};var labels=[['contacts','کانال‌های ارتباطی'],['ai','دستیار هوشمند'],['home','صفحه نخست'],['media','رسانه و حرکت'],['tools','ابزارهای محصول']];var nav=document.createElement('nav');nav.className='bhc-settings-tabs';nav.setAttribute('aria-label','بخش‌های تنظیمات');labels.forEach(function(g,i){var b=document.createElement('button');b.type='button';b.textContent=g[1];b.dataset.group=g[0];b.className=i===0?'is-active':'';nav.appendChild(b);});var note=document.createElement('p');note.className='bhc-tab-note';note.textContent='ذخیره، فیلدهای تب‌های دیگر را هم نگه می‌دارد. فیلد پنهان حذف نمی‌شود.';table.parentNode.insertBefore(note,table);table.parentNode.insertBefore(nav,note);rows.forEach(function(row){var th=row.querySelector('th');var title=th?th.textContent.replace(/\s+/g,' ').trim():'';row.dataset.settingsGroup=headings[title]||'contacts';});function activate(name){nav.querySelectorAll('button').forEach(function(b){var on=b.dataset.group===name;b.classList.toggle('is-active',on);b.setAttribute('aria-pressed',on?'true':'false');});rows.forEach(function(row){row.hidden=row.dataset.settingsGroup!==name;});}nav.addEventListener('click',function(e){var button=e.target.closest('button');if(button)activate(button.dataset.group);});activate('contacts');
document.querySelectorAll('.bhc-seal-pick').forEach(function(button){
 button.addEventListener('click',function(){
  if(!window.wp||!wp.media)return;
  var frame=wp.media({title:'تصویر نشان',button:{text:'استفاده از این تصویر'},multiple:false,library:{type:'image'}});
  frame.on('select',function(){
   var file=frame.state().get('selection').first().toJSON();
   var input=button.parentNode.querySelector('.bhc-seal-image');
   var preview=button.closest('.bhc-seal').querySelector('.bhc-seal-preview');
   if(input)input.value=file.url||'';
   if(preview)preview.innerHTML=file.url?'<img alt="" src="'+file.url.replace(/"/g,'')+'">':'<span>پیش‌نمایش نشان</span>';
   var card=button.closest('.bhc-seal');
   var link=card&&card.querySelector('input[type="url"]');
   var ready=!!(input&&input.value&&link&&link.value);
   card.classList.toggle('is-ready',ready);
   var state=card.querySelector('.bhc-seal-state');
   if(state)state.textContent=ready?'آماده نمایش':'ناقص';
  });
  frame.open();
 });
});
});})();
