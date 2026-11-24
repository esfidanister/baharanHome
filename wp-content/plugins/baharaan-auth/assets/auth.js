(function () {
  var cfg = window.bhaAuth || {};
  var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (reduced) document.documentElement.classList.add('bha-reduce');

  function digits(value) {
    return String(value || '').replace(/[۰-۹]/g, function (ch) {
      return '۰۱۲۳۴۵۶۷۸۹'.indexOf(ch);
    }).replace(/[٠-٩]/g, function (ch) {
      return '٠١٢٣٤٥٦٧٨٩'.indexOf(ch);
    }).replace(/\D/g, '');
  }

  var form = document.getElementById('bha-form');
  if (form) setupAuth(form);
  var profile = document.getElementById('bha-profile-form');
  if (profile) setupProfile(profile);
  setupParallax();

  function setupAuth(form) {
    var status = document.getElementById('bha-status');
    var channel = form.getAttribute('data-channel') || 'mobile';
    var resendIn = 0;
    var timer = 0;
    var panels = {
      identify: form.querySelector('[data-panel="identify"]'),
      code: form.querySelector('[data-panel="code"]'),
      password: form.querySelector('[data-panel="password"]'),
      reset: form.querySelector('[data-panel="reset"]')
    };

    function setStatus(text, kind) {
      status.textContent = text || '';
      status.className = 'bha-status' + (text ? ' is-' + kind : '');
      if (kind === 'error') status.setAttribute('role', 'alert');
      else status.setAttribute('role', 'status');
    }

    function show(name) {
      Object.keys(panels).forEach(function (key) {
        panels[key].hidden = key !== name;
      });
      var methods = document.querySelector('.bha-methods');
      var passwordLink = document.querySelector('[data-show-password]');
      if (methods) methods.hidden = name === 'code' || name === 'reset';
      if (passwordLink) passwordLink.hidden = name !== 'identify';
      var focus = form.querySelector('[data-panel="' + name + '"] input');
      if (focus) window.setTimeout(function () { focus.focus(); }, reduced ? 0 : 40);
    }

    function setChannel(next) {
      channel = next;
      form.setAttribute('data-channel', next);
      var input = document.getElementById('bha-identifier');
      var label = document.getElementById('bha-id-label');
      var hint = document.getElementById('bha-hint');
      var email = next === 'email';
      input.type = email ? 'email' : 'tel';
      input.inputMode = email ? 'email' : 'numeric';
      input.autocomplete = email ? 'email' : 'tel';
      input.placeholder = email ? 'name@example.com' : '09120000000';
      label.textContent = email ? 'ایمیل' : 'شماره موبایل';
      hint.textContent = email ? 'کد تأیید به همین ایمیل فرستاده می‌شود.' : 'کد یک‌بارمصرف پیامک می‌شود. اگر حساب نداشته باشید، پس از تأیید شماره ساخته می‌شود.';
      document.querySelectorAll('.bha-methods button').forEach(function (button) {
        var active = button.getAttribute('data-channel') === next;
        button.classList.toggle('is-active', active);
        button.setAttribute('aria-selected', active ? 'true' : 'false');
      });
      var panel = document.getElementById('bha-panel');
      if (panel) panel.setAttribute('aria-labelledby', 'bha-tab-' + next);
      show('identify');
      input.focus();
    }

    document.querySelectorAll('.bha-methods button').forEach(function (button) {
      button.addEventListener('click', function () { setChannel(button.getAttribute('data-channel')); });
    });
    var passwordButton = document.querySelector('[data-show-password]');
    if (passwordButton) passwordButton.addEventListener('click', function () { show('password'); });
    var back = form.querySelector('[data-back]');
    var reset = form.querySelector('[data-reset]');
    var backPassword = form.querySelector('[data-back-password]');
    if (back) back.addEventListener('click', function () { show('identify'); });
    if (reset) reset.addEventListener('click', function () { show('reset'); });
    if (backPassword) backPassword.addEventListener('click', function () { show('password'); });
    var eye = form.querySelector('.bha-eye');
    if (eye) eye.addEventListener('click', function () {
      var input = document.getElementById('bha-password');
      var visible = input.type === 'text';
      input.type = visible ? 'password' : 'text';
      eye.setAttribute('aria-pressed', visible ? 'false' : 'true');
      eye.setAttribute('aria-label', visible ? 'نمایش رمز عبور' : 'پنهان کردن رمز عبور');
    });

    function paintResend() {
      var button = form.querySelector('[data-resend]');
      if (!button) return;
      if (resendIn <= 0) {
        button.disabled = false;
        button.textContent = 'ارسال مجدد';
        window.clearInterval(timer);
        return;
      }
      button.disabled = true;
      button.textContent = 'ارسال مجدد تا ' + resendIn + ' ثانیه';
    }
    function startResend(seconds) {
      resendIn = Math.max(0, Number(seconds) || 0);
      window.clearInterval(timer);
      paintResend();
      if (resendIn <= 0) return;
      timer = window.setInterval(function () {
        resendIn -= 1;
        paintResend();
      }, 1000);
    }

    form.querySelector('[data-resend]').addEventListener('click', function () {
      submit(form.querySelector('[data-intent="send"]'), true);
    });

    form.addEventListener('submit', function (event) {
      event.preventDefault();
      var button = event.submitter || form.querySelector('button[type="submit"]:not([hidden])');
      var visible = form.querySelector('[data-panel]:not([hidden]) [data-intent]');
      submit(visible || button, false);
    });

    function mark(input, invalid) {
      if (!input) return;
      if (invalid) input.setAttribute('aria-invalid', 'true');
      else input.removeAttribute('aria-invalid');
    }

    function submit(button, isResend) {
      var intent = button.getAttribute('data-intent');
      var body = new FormData();
      body.set('action', 'bha_auth');
      body.set('nonce', cfg.nonce || '');
      body.set('intent', intent);
      body.set('redirect_to', cfg.redirect || form.querySelector('[name="redirect_to"]').value);
      body.set('company', form.querySelector('[name="company"]').value);
      var field = null;
      if (intent === 'send' || intent === 'verify') {
        field = document.getElementById('bha-identifier');
        var raw = field.value.trim();
        if (channel === 'mobile') {
          var mobile = digits(raw);
          if (mobile.indexOf('98') === 0 && mobile.length === 12) mobile = '0' + mobile.slice(2);
          if (!/^09\d{9}$/.test(mobile)) {
            mark(field, true);
            setStatus('شماره موبایل باید با ۰۹ شروع شود و ۱۱ رقم باشد.', 'error');
            field.focus();
            return;
          }
          body.set('identifier', mobile);
        } else if (!/^\S+@\S+\.\S+$/.test(raw)) {
          mark(field, true);
          setStatus('یک نشانی ایمیل معتبر وارد کنید.', 'error');
          field.focus();
          return;
        } else body.set('identifier', raw);
        body.set('channel', channel);
        if (intent === 'verify') {
          var codeField = document.getElementById('bha-code');
          var code = digits(codeField.value);
          if (code.length !== 6) {
            mark(codeField, true);
            setStatus('کد تأیید باید ۶ رقم باشد.', 'error');
            codeField.focus();
            return;
          }
          body.set('code', code);
          mark(codeField, false);
        }
        mark(field, false);
      } else if (intent === 'password') {
        var login = document.getElementById('bha-login');
        var password = document.getElementById('bha-password');
        if (!login.value.trim() || !password.value) {
          mark(login, !login.value.trim());
          mark(password, !password.value);
          setStatus('شناسه و رمز عبور را وارد کنید.', 'error');
          (login.value.trim() ? password : login).focus();
          return;
        }
        body.set('login', login.value.trim());
        body.set('password', password.value);
        if (form.querySelector('[name="remember"]').checked) body.set('remember', '1');
        mark(login, false);
        mark(password, false);
      } else if (intent === 'reset') {
        var email = document.getElementById('bha-reset-email');
        if (!/^\S+@\S+\.\S+$/.test(email.value.trim())) {
          mark(email, true);
          setStatus('برای بازیابی رمز، ایمیل حساب را وارد کنید.', 'error');
          email.focus();
          return;
        }
        body.set('identifier', email.value.trim());
        mark(email, false);
      }

      button.disabled = true;
      button.classList.remove('is-success', 'is-error');
      button.classList.add('is-loading');
      button.setAttribute('aria-busy', 'true');
      setStatus(isResend ? 'در حال ارسال مجدد کد…' : 'در حال بررسی…', 'info');
      fetch(cfg.ajax, { method: 'POST', body: body, credentials: 'same-origin' })
        .then(function (response) { return response.json().then(function (data) { return { ok: response.ok, data: data }; }); })
        .then(function (result) {
          var payload = result.data && (result.data.data || result.data) || {};
          var message = payload.message || 'ارتباط با سرور برقرار نشد.';
          if (!result.ok || result.data.success === false) {
            button.classList.add('is-error');
            setStatus(message, 'error');
            if (payload.code === 'bad_code' || payload.code === 'expired' || payload.code === 'locked') {
              var codeField = document.getElementById('bha-code');
              mark(codeField, true);
              show('code');
            }
            return;
          }
          button.classList.remove('is-error');
          button.classList.add('is-success');
          setStatus(message, 'ok');
          if (payload.step === 'code') {
            var hint = document.getElementById('bha-code-hint');
            hint.textContent = '';
            hint.append('کد برای ');
            var masked = document.createElement('bdi');
            masked.dir = 'ltr';
            masked.textContent = payload.masked || '';
            hint.append(masked);
            hint.append(' فرستاده شد.');
            show('code');
            startResend(payload.resend_in || cfg.resend || 60);
          } else if (payload.redirect) {
            window.setTimeout(function () { window.location.assign(payload.redirect); }, reduced ? 0 : 450);
          }
        })
        .catch(function () {
          button.classList.add('is-error');
          setStatus('ارتباط با سرور قطع شد. اتصال را بررسی کنید و دوباره تلاش کنید.', 'error');
        })
        .finally(function () {
          button.classList.remove('is-loading');
          button.removeAttribute('aria-busy');
          if (!button.classList.contains('is-success')) button.disabled = false;
        });
    }
  }

  function setupProfile(profile) {
    var status = document.getElementById('bha-profile-status');
    profile.addEventListener('submit', function (event) {
      event.preventDefault();
      var button = profile.querySelector('button');
      var body = new FormData(profile);
      body.set('action', 'bha_auth');
      body.set('intent', 'profile');
      body.set('nonce', cfg.nonce || '');
      button.disabled = true;
      fetch(cfg.ajax, { method: 'POST', body: body, credentials: 'same-origin' })
        .then(function (response) { return response.json().then(function (data) { return { ok: response.ok, data: data }; }); })
        .then(function (result) {
          var payload = result.data.data || {};
          status.textContent = payload.message || 'ذخیره انجام نشد.';
          status.className = 'bha-status is-' + (result.ok && result.data.success ? 'ok' : 'error');
          if (result.ok && result.data.success && payload.redirect) {
            window.setTimeout(function () { window.location.assign(payload.redirect); }, 500);
          }
        })
        .catch(function () {
          status.textContent = 'ارتباط با سرور قطع شد.';
          status.className = 'bha-status is-error';
        })
        .finally(function () { button.disabled = false; });
    });
  }

  function setupParallax() {
    if (!cfg.parallax || reduced || window.innerWidth < 980) return;
    if (!window.matchMedia('(pointer:fine)').matches) return;
    var scene = document.querySelector('.bha-scene');
    var stage = document.querySelector('.bha-stage');
    if (!scene || !stage) return;
    var live = true;
    var observer = new IntersectionObserver(function (entries) {
      live = entries[0] ? entries[0].isIntersecting : false;
      if (!live) reset();
    });
    observer.observe(stage);
    function reset() {
      scene.querySelectorAll('.bha-product').forEach(function (el) {
        el.style.setProperty('--px', '0px');
        el.style.setProperty('--py', '0px');
      });
    }
    window.addEventListener('pointermove', function (event) {
      if (!live || document.hidden) return;
      var x = event.clientX / window.innerWidth - 0.5;
      var y = event.clientY / window.innerHeight - 0.5;
      scene.querySelectorAll('.bha-product').forEach(function (el) {
        var depth = Number(el.dataset.depth || 6);
        el.style.setProperty('--px', (x * depth) + 'px');
        el.style.setProperty('--py', (y * depth) + 'px');
      });
    }, { passive: true });
    document.addEventListener('visibilitychange', function () { if (document.hidden) reset(); });
  }
})();
