(function(){'use strict';
document.addEventListener('DOMContentLoaded',function(){
 var root=document.querySelector('.bhc-studio-wrap');
 if(!root) return;
 var nav=root.querySelector('.bhc-studio-tabs');
 var panels=root.querySelectorAll('[data-studio-group]');
 if(!nav||!panels.length) return;
 function groupOf(id){
  var node=id?document.getElementById(id):null;
  return node&&node.dataset.studioGroup?node.dataset.studioGroup:'';
 }
 function activate(name,focusId){
  if(!name) name='overview';
  nav.querySelectorAll('[data-group]').forEach(function(button){
   var on=button.dataset.group===name;
   button.classList.toggle('is-active',on);
   button.setAttribute('aria-selected',on?'true':'false');
  });
  panels.forEach(function(panel){panel.hidden=panel.dataset.studioGroup!==name;});
  if(focusId){
   var target=document.getElementById(focusId);
   if(target) target.scrollIntoView({behavior:'smooth',block:'start'});
  }
 }
 nav.addEventListener('click',function(event){
  var button=event.target.closest('[data-group]');
  if(!button||!nav.contains(button)) return;
  event.preventDefault();
  activate(button.dataset.group);
  if(history.replaceState) history.replaceState(null,'','#'+button.dataset.group);
 });
 root.addEventListener('click',function(event){
  var jump=event.target.closest('[data-studio-jump], a[href^="#studio-"]');
  if(!jump||!root.contains(jump)) return;
  var id=(jump.getAttribute('data-studio-jump')||jump.getAttribute('href')||'').replace(/^#/,'');
  var name=groupOf(id);
  if(!name) return;
  event.preventDefault();
  activate(name,id);
  if(history.replaceState) history.replaceState(null,'','#'+id);
 });
 var hash=(location.hash||'').replace(/^#/,'');
 var fromHash=groupOf(hash)|| (nav.querySelector('[data-group="'+hash+'"]')?hash:'');
 activate(fromHash||'overview', groupOf(hash)?hash:'');
});
})();
