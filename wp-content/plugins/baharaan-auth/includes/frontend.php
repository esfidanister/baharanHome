<?php
if (!defined('ABSPATH')) {
    exit;
}

add_filter('woocommerce_locate_template', function ($template, $name) {
    if ($name === 'myaccount/form-login.php' && !is_user_logged_in() && bha_uses_own_login()) {
        $custom = BHA_DIR . 'templates/form-login.php';
        if (file_exists($custom)) {
            return $custom;
        }
    }
    return $template;
}, 20, 2);

add_filter('body_class', function ($classes) {
    if (function_exists('is_account_page') && is_account_page() && !is_user_logged_in() && bha_uses_own_login()) {
        $classes[] = 'bha-guest';
        if (empty(bha_settings()['motion'])) {
            $classes[] = 'bha-motion-off';
        }
    }
    return $classes;
});

add_filter('document_title_parts', function ($parts) {
    if (function_exists('is_account_page') && is_account_page() && !is_user_logged_in() && bha_uses_own_login()) {
        $parts['title'] = 'ورود به بهاران هوم';
    }
    return $parts;
});

add_action('wp', function () {
    if (function_exists('is_account_page') && is_account_page() && !is_user_logged_in() && bha_uses_own_login()) {
        remove_action('woocommerce_before_customer_login_form', 'bhc_otp_form');
    }
});

add_action('wp_enqueue_scripts', function () {
    if (!function_exists('is_account_page') || !is_account_page()) {
        return;
    }
    if (!is_user_logged_in()) {
        if (!bha_uses_own_login()) {
            return;
        }
        wp_enqueue_style('bha-auth', BHA_URL . 'assets/auth.css', [], BHA_VERSION);
        wp_enqueue_script('bha-auth', BHA_URL . 'assets/auth.js', [], BHA_VERSION, true);
        $settings = bha_settings();
        wp_localize_script('bha-auth', 'bhaAuth', [
            'ajax' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('bha_auth'),
            'redirect' => bha_guest_redirect(),
            'motion' => (bool) $settings['motion'],
            'float' => (bool) $settings['float'],
            'parallax' => (bool) $settings['parallax'],
            'duration' => (int) $settings['motion_ms'],
            'resend' => (int) $settings['resend'],
        ]);
    } else {
        wp_enqueue_style('bha-auth', BHA_URL . 'assets/auth.css', [], BHA_VERSION);
        wp_enqueue_script('bha-auth', BHA_URL . 'assets/auth.js', [], BHA_VERSION, true);
        wp_localize_script('bha-auth', 'bhaAuth', [
            'ajax' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('bha_auth'),
            'redirect' => bha_safe_redirect(''),
            'profile' => true,
        ]);
    }
}, 120);

function bha_guest_redirect() {
    $candidate = '';
    if (isset($_GET['redirect_to'])) {
        $candidate = wp_unslash($_GET['redirect_to']);
    } elseif (isset($_GET['redirect'])) {
        $candidate = wp_unslash($_GET['redirect']);
    }
    return bha_safe_redirect($candidate);
}

function bha_render_auth_shell($args = []) {
    $preview = !empty($args['preview']);
    $mobile = !empty($args['mobile']);
    $s = bha_settings();
    $methods = [];
    if (!empty($s['mobile_login'])) {
        $methods['mobile'] = 'شماره موبایل';
    }
    if (!empty($s['email_login'])) {
        $methods['email'] = 'ایمیل';
    }
    if (!$methods && !empty($s['password_login'])) {
        $methods['mobile'] = 'شماره موبایل';
    }
    $first = array_key_first($methods) ?: 'mobile';
    $images = [
        'fridge' => bha_image_url('img_fridge', 'assets/images/catalog/refrigerator.webp'),
        'washer' => bha_image_url('img_washer', 'assets/images/generated/bento-washer.webp'),
        'coffee' => bha_image_url('img_coffee', 'assets/images/catalog/coffee.webp'),
        'fryer' => bha_image_url('img_fryer', 'assets/images/generated/bento-airfryer.webp'),
        'mobile' => bha_image_url('img_mobile', 'assets/images/catalog/refrigerator.webp'),
    ];
    $style = sprintf(
        '--bha-primary:%1$s;--bha-secondary:%2$s;--bha-navy:%3$s;--bha-ice:%4$s;--bha-light:%5$s;--bha-text:%6$s;--bha-border:%7$s;--bha-radius:%8$dpx;--bha-dur:%9$dms;',
        $s['primary'],
        $s['secondary'],
        $s['navy'],
        $s['ice'],
        $s['light'],
        $s['text'],
        $s['border'],
        (int) $s['radius'],
        (int) $s['motion_ms']
    );
    $classes = 'bha-stage';
    if ($preview) {
        $classes .= ' is-preview';
    }
    if ($mobile) {
        $classes .= ' is-mobile-frame';
    }
    if (empty($s['motion'])) {
        $classes .= ' is-still';
    }
    if (empty($s['float'])) {
        $classes .= ' is-no-float';
    }
    if (!empty($s['button_fx'])) {
        $classes .= ' has-button-fx';
    }
    echo '<section class="' . esc_attr($classes) . '" style="' . esc_attr($style) . '" aria-labelledby="bha-title">';
    echo '<div class="bha-scene" data-parallax="' . (empty($s['parallax']) ? '0' : '1') . '" aria-hidden="true">';
    echo '<span class="bha-arc bha-arc-a"></span><span class="bha-arc bha-arc-b"></span><span class="bha-glow"></span>';
    $sizes = ['fridge' => [574, 1011], 'washer' => [636, 877], 'coffee' => [714, 943], 'fryer' => [802, 800]];
    foreach ([
        'fridge' => 'یخچال سایدبای‌ساید',
        'washer' => 'ماشین لباسشویی',
        'coffee' => 'قهوه‌ساز',
        'fryer' => 'سرخ‌کن',
    ] as $slot => $label) {
        if ($images[$slot] === '') {
            continue;
        }
        echo '<figure class="bha-product bha-product--' . esc_attr($slot) . '" data-depth="' . esc_attr($slot === 'fridge' || $slot === 'coffee' ? '10' : '6') . '">';
        echo '<img src="' . esc_url($images[$slot]) . '" alt="" width="' . (int) $sizes[$slot][0] . '" height="' . (int) $sizes[$slot][1] . '" decoding="async">';
        echo '<figcaption>' . esc_html($label) . '</figcaption></figure>';
    }
    echo '</div>';
    echo '<div class="bha-mobile-hero" aria-hidden="true">';
    foreach (['fridge', 'coffee', 'fryer'] as $slot) {
        if ($images[$slot] === '') {
            continue;
        }
        echo '<img src="' . esc_url($images[$slot]) . '" alt="" width="' . (int) $sizes[$slot][0] . '" height="' . (int) $sizes[$slot][1] . '" decoding="async">';
    }
    echo '</div>';
    echo '<div class="bha-card" id="bha-auth-card">';
    echo '<div class="bha-brand"><span class="bha-mark" aria-hidden="true"><svg viewBox="0 0 48 48" width="28" height="28"><path d="M8 22 24 9l16 13v16a3 3 0 0 1-3 3H11a3 3 0 0 1-3-3V22z" fill="none" stroke="currentColor" stroke-width="2.4"/><path d="M20 40V27h8v13" fill="none" stroke="currentColor" stroke-width="2.4"/></svg></span><strong>بهاران هوم</strong></div>';
    echo '<h1 id="bha-title">' . esc_html($s['welcome']) . '</h1>';
    echo '<p class="bha-lead">' . esc_html($s['lead']) . '</p>';
    if (count($methods) > 1) {
        echo '<div class="bha-methods" role="tablist" aria-label="روش ورود">';
        foreach ($methods as $id => $label) {
            echo '<button type="button" role="tab" id="bha-tab-' . esc_attr($id) . '" aria-controls="bha-panel" aria-selected="' . ($id === $first ? 'true' : 'false') . '" data-channel="' . esc_attr($id) . '"' . ($id === $first ? ' class="is-active"' : '') . '>' . esc_html($label) . '</button>';
        }
        echo '</div>';
    }
    echo '<form id="bha-form" method="post" action="' . esc_url(admin_url('admin-ajax.php')) . '" novalidate data-channel="' . esc_attr($first) . '"' . ($preview ? ' onsubmit="return false"' : '') . '>';
    echo '<input type="hidden" name="action" value="bha_auth">';
    echo '<input type="hidden" name="nonce" value="' . esc_attr($preview ? '' : wp_create_nonce('bha_auth')) . '">';
    echo '<input type="hidden" name="redirect_to" value="' . esc_attr($preview ? '' : bha_guest_redirect()) . '">';
    echo '<div class="bha-hp" hidden><label>شرکت <input type="text" name="company" tabindex="-1" autocomplete="off"></label></div>';
    echo '<div id="bha-panel" role="tabpanel" aria-labelledby="bha-tab-' . esc_attr($first) . '">';
    echo '<div data-panel="identify">';
    echo '<label id="bha-id-label" for="bha-identifier">' . ($first === 'email' ? 'ایمیل' : 'شماره موبایل') . '</label>';
    echo '<input id="bha-identifier" name="identifier" type="' . ($first === 'email' ? 'email' : 'tel') . '" inputmode="' . ($first === 'email' ? 'email' : 'numeric') . '" autocomplete="' . ($first === 'email' ? 'email' : 'tel') . '" placeholder="' . ($first === 'email' ? 'name@example.com' : '09120000000') . '" required>';
    echo '<p class="bha-hint" id="bha-hint">' . ($first === 'email' ? 'کد تأیید به همین ایمیل فرستاده می‌شود.' : 'کد یک‌بارمصرف پیامک می‌شود. اگر حساب نداشته باشید، پس از تأیید شماره ساخته می‌شود.') . '</p>';
    echo '<button class="bha-submit" type="submit" data-intent="send"><span>' . esc_html($s['button']) . '</span></button>';
    echo '</div>';
    echo '<div data-panel="code" hidden>';
    echo '<label for="bha-code">کد تأیید</label>';
    echo '<input id="bha-code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code" maxlength="6" pattern="[0-9]{6}" placeholder="------">';
    echo '<p class="bha-hint" id="bha-code-hint">کد را وارد کنید.</p>';
    echo '<button class="bha-submit" type="submit" data-intent="verify"><span>تأیید و ورود</span></button>';
    echo '<div class="bha-row"><button type="button" class="bha-text" data-back>تغییر مشخصات</button><button type="button" class="bha-text" data-resend disabled>ارسال مجدد</button></div>';
    echo '</div>';
    echo '<div data-panel="password" hidden>';
    echo '<label for="bha-login">موبایل، ایمیل یا نام کاربری</label>';
    echo '<input id="bha-login" name="login" type="text" autocomplete="username">';
    echo '<label for="bha-password">رمز عبور</label>';
    echo '<div class="bha-password"><input id="bha-password" name="password" type="password" autocomplete="current-password"><button type="button" class="bha-eye" aria-label="نمایش رمز عبور" aria-pressed="false"><svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12z" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="2.6" fill="none" stroke="currentColor" stroke-width="1.8"/></svg></button></div>';
    echo '<label class="bha-remember"><input type="checkbox" name="remember" value="1" checked> مرا به خاطر بسپار</label>';
    echo '<button class="bha-submit" type="submit" data-intent="password"><span>ورود</span></button>';
    echo '<button type="button" class="bha-text" data-reset>رمز عبور را فراموش کرده‌ام</button>';
    echo '</div>';
    echo '<div data-panel="reset" hidden>';
    echo '<label for="bha-reset-email">ایمیل حساب</label>';
    echo '<input id="bha-reset-email" name="reset_email" type="email" autocomplete="email" placeholder="name@example.com">';
    echo '<p class="bha-hint">اگر حسابی با این ایمیل باشد، لینک ساخت رمز جدید فرستاده می‌شود.</p>';
    echo '<button class="bha-submit" type="submit" data-intent="reset"><span>ارسال لینک بازیابی</span></button>';
    echo '<button type="button" class="bha-text" data-back-password>بازگشت به ورود با رمز</button>';
    echo '</div>';
    echo '</div>';
    echo '<p id="bha-status" class="bha-status" aria-live="polite"></p>';
    echo '</form>';
    if (!empty($s['password_login'])) {
        echo '<button type="button" class="bha-switch" data-show-password>ورود با رمز عبور</button>';
    }
    echo '<p class="bha-secure"><span aria-hidden="true">◌</span> ورود امن و مطمئن. کدها پس از استفاده باطل می‌شوند و حساب مدیریت با این فرم باز نمی‌شود.</p>';
    echo '<a class="bha-back" href="' . esc_url(function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/')) . '">بازگشت به فروشگاه</a>';
    echo '</div></section>';
    if (!$preview) {
        echo '<noscript><p class="bha-noscript">برای ورود با کد، جاوااسکریپت لازم است. می‌توانید با نام کاربری و رمز از صفحه مدیریت‌شده ورود استفاده کنید.</p></noscript>';
    }
}

add_action('woocommerce_account_dashboard', function () {
    if (!is_user_logged_in()) {
        return;
    }
    $user = wp_get_current_user();
    $needs = get_user_meta($user->ID, '_bha_needs_profile', true) || (isset($_GET['bha_profile']) && sanitize_key(wp_unslash($_GET['bha_profile'])) === '1');
    if (!$needs || bha_privileged($user)) {
        return;
    }
    $email = $user->user_email;
    $placeholder = substr($email, -8) === '.invalid';
    echo '<section class="bha-profile" aria-labelledby="bha-profile-title">';
    echo '<h2 id="bha-profile-title">تکمیل حساب</h2>';
    echo '<p>نام شما برای سفارش و پشتیبانی لازم است. ایمیل را هم اگر در دسترس است اضافه کنید.</p>';
    echo '<form id="bha-profile-form">';
    echo '<label for="bha-first">نام</label><input id="bha-first" name="first_name" required autocomplete="given-name" value="' . esc_attr($user->first_name) . '">';
    echo '<label for="bha-last">نام خانوادگی</label><input id="bha-last" name="last_name" autocomplete="family-name" value="' . esc_attr($user->last_name) . '">';
    echo '<label for="bha-mail">ایمیل</label><input id="bha-mail" name="email" type="email" autocomplete="email" value="' . esc_attr($placeholder ? '' : $email) . '" placeholder="اختیاری">';
    echo '<button type="submit">ذخیره مشخصات</button>';
    echo '<p class="bha-status" id="bha-profile-status" aria-live="polite"></p>';
    echo '</form></section>';
}, 4);

add_filter('woocommerce_login_redirect', function ($redirect, $user) {
    if (!bha_uses_own_login()) {
        return $redirect;
    }
    if ($user instanceof WP_User && bha_privileged($user)) {
        return $redirect;
    }
    $candidate = isset($_REQUEST['redirect_to']) ? wp_unslash($_REQUEST['redirect_to']) : $redirect;
    return bha_safe_redirect($candidate);
}, 20, 2);
