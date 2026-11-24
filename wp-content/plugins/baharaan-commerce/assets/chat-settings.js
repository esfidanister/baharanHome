(function(){'use strict';
document.addEventListener('DOMContentLoaded',function(){
 const form=document.querySelector('.bhc-admin form[action="options.php"]');
 const table=form&&form.querySelector('.form-table');
 if(!table)return;
 const row=document.createElement('tr');
 row.dataset.settingsGroup='media';
 row.innerHTML='<th scope="row">تصویر چت پشتیبانی</th><td><div class="bhc-avatar-picker"><img class="bhc-avatar-preview" alt="پیش‌نمایش تصویر پشتیبانی"><input class="regular-text" type="url" name="bhc_options[chat_avatar]" placeholder="نشانی تصویر یا انتخاب از رسانه‌ها"><button type="button" class="button bhc-avatar-select">انتخاب تصویر</button><button type="button" class="button bhc-avatar-clear">حذف تصویر</button></div><p class="description">تصویر مربعی یا PNG شفاف پیشنهاد می‌شود. این تصویر در دکمه شناور و سربرگ گفت‌وگو نمایش داده می‌شود.</p></td>';
 const first=table.querySelector('tr');
 table.insertBefore(row,first);
 const input=row.querySelector('input'),preview=row.querySelector('.bhc-avatar-preview');
 function refresh(){preview.src=input.value||'';preview.hidden=!input.value;}
 input.addEventListener('input',refresh);refresh();
 row.querySelector('.bhc-avatar-select').addEventListener('click',function(){
  if(!window.wp||!wp.media)return;
  const frame=wp.media({title:'انتخاب تصویر چت پشتیبانی',button:{text:'استفاده از تصویر'},multiple:false,library:{type:'image'}});
  frame.on('select',function(){const file=frame.state().get('selection').first().toJSON();input.value=file.url||'';refresh()});
  frame.open();
 });
 row.querySelector('.bhc-avatar-clear').addEventListener('click',function(){input.value='';refresh()});
});})();
