(function () {
  'use strict';
  var range = document.getElementById('bh-range');
  var refresh = document.getElementById('bh-refresh');
  if (!range || !refresh) return;
  var custom = document.querySelector('.bh-custom-range');
  ['bh-after', 'bh-before'].forEach(function (id) {
    var field = document.getElementById(id);
    if (field) { field.type = 'text'; field.inputMode = 'numeric'; field.placeholder = '۱۴۰۴/۰۱/۰۱'; }
  });
  range.addEventListener('change', function () { if (custom) custom.hidden = range.value !== 'custom'; });
  var saved = window.localStorage ? localStorage.getItem('bh-dashboard-range') : null;
  if (saved && range.querySelector('option[value="' + saved + '"]')) range.value = saved;
  range.addEventListener('change', function () { if (window.localStorage) localStorage.setItem('bh-dashboard-range', range.value); });
  refresh.addEventListener('click', function () {
    refresh.disabled = true;
    refresh.classList.add('is-busy');
    refresh.textContent = 'در حال تازه‌سازی…';
    window.setTimeout(function () { window.location.reload(); }, 150);
  });
  var ordersLink = document.querySelector('.bh-main-grid .bh-text-link');
  if (ordersLink) ordersLink.href = 'admin.php?page=wc-orders';
}());

