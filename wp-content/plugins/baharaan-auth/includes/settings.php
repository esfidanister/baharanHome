<?php
if (!defined('ABSPATH')) {
    exit;
}

function bha_default_settings() {
    return [
        'primary' => '#0755D8',
        'secondary' => '#168BFF',
        'navy' => '#102443',
        'ice' => '#F4F9FF',
        'light' => '#EAF4FF',
        'text' => '#152B49',
        'border' => '#DCE9F8',
        'radius' => 26,
        'welcome' => 'خوش آمدید!',
        'lead' => 'برای ورود یا ساخت حساب کاربری، روش مورد نظر خود را انتخاب کنید.',
        'button' => 'ادامه',
        'motion' => 1,
        'motion_ms' => 700,
        'float' => 1,
        'parallax' => 1,
        'button_fx' => 1,
        'mobile_login' => 1,
        'email_login' => 1,
        'password_login' => 1,
        'otp_ttl' => 120,
        'resend' => 60,
        'max_tries' => 5,
        'send_limit' => 6,
        'allow_register' => 1,
        'redirect' => '',
        'sms_provider' => 'apiir',
        'login_surface' => 'baharaan',
        'sms_template' => 'کد ورود بهاران هوم: {code}',
        'email_subject' => 'کد ورود بهاران هوم',
        'email_body' => "کد تأیید شما: {code}\nاین کد {minutes} دقیقه معتبر است. اگر این درخواست را شما نداده‌اید، این نامه را نادیده بگیرید.",
        'session_days' => 14,
        'img_fridge' => 0,
        'img_washer' => 0,
        'img_coffee' => 0,
        'img_fryer' => 0,
        'img_mobile' => 0,
    ];
}

function bha_uses_own_login() {
    return (bha_settings()['login_surface'] ?? 'baharaan') !== 'external';
}

function bha_settings() {
    $saved = get_option('bha_settings', []);
    $settings = wp_parse_args(is_array($saved) ? $saved : [], bha_default_settings());
    $settings['radius'] = max(16, min(36, (int) $settings['radius']));
    $settings['motion_ms'] = max(400, min(900, (int) $settings['motion_ms']));
    $settings['otp_ttl'] = max(60, min(600, (int) $settings['otp_ttl']));
    $settings['resend'] = max(30, min(300, (int) $settings['resend']));
    $settings['max_tries'] = max(3, min(8, (int) $settings['max_tries']));
    $settings['send_limit'] = max(3, min(20, (int) $settings['send_limit']));
    $settings['session_days'] = max(1, min(30, (int) $settings['session_days']));
    return $settings;
}

function bha_color($value, $fallback) {
    $color = sanitize_hex_color((string) $value);
    return $color ? $color : $fallback;
}

function bha_sanitize_settings($input) {
    $old = bha_settings();
    $in = is_array($input) ? $input : [];
    $tab = sanitize_key($in['tab'] ?? '');
    $next = $old;
    $flag = function ($key) use ($in) {
        return empty($in[$key]) ? 0 : 1;
    };
    if ($tab === 'design') {
        $defaults = bha_default_settings();
        foreach (['primary', 'secondary', 'navy', 'ice', 'light', 'text', 'border'] as $key) {
            $next[$key] = bha_color($in[$key] ?? '', $defaults[$key]);
        }
        $next['radius'] = max(16, min(36, absint($in['radius'] ?? 26)));
        $next['welcome'] = sanitize_text_field($in['welcome'] ?? $old['welcome']);
        $next['lead'] = sanitize_text_field($in['lead'] ?? $old['lead']);
        $next['button'] = sanitize_text_field($in['button'] ?? $old['button']);
        foreach (['img_fridge', 'img_washer', 'img_coffee', 'img_fryer', 'img_mobile'] as $key) {
            $next[$key] = absint($in[$key] ?? 0);
        }
    } elseif ($tab === 'motion') {
        $next['motion'] = $flag('motion');
        $next['float'] = $flag('float');
        $next['parallax'] = $flag('parallax');
        $next['button_fx'] = $flag('button_fx');
        $next['motion_ms'] = max(400, min(900, absint($in['motion_ms'] ?? 700)));
    } elseif ($tab === 'auth') {
        $next['mobile_login'] = $flag('mobile_login');
        $next['email_login'] = $flag('email_login');
        $next['password_login'] = $flag('password_login');
        $next['allow_register'] = $flag('allow_register');
        $next['otp_ttl'] = max(60, min(600, absint($in['otp_ttl'] ?? 120)));
        $next['resend'] = max(30, min(300, absint($in['resend'] ?? 60)));
        $next['max_tries'] = max(3, min(8, absint($in['max_tries'] ?? 5)));
        $next['send_limit'] = max(3, min(20, absint($in['send_limit'] ?? 6)));
        $next['session_days'] = max(1, min(30, absint($in['session_days'] ?? 14)));
        $next['redirect'] = esc_url_raw($in['redirect'] ?? '');
        $surface = sanitize_key($in['login_surface'] ?? 'baharaan');
        $next['login_surface'] = $surface === 'external' ? 'external' : 'baharaan';
        if (!$next['mobile_login'] && !$next['email_login'] && !$next['password_login']) {
            $next['password_login'] = 1;
        }
    } elseif ($tab === 'delivery') {
        $provider = sanitize_key($in['sms_provider'] ?? '');
        $known = array_keys(bha_sms_providers());
        $known[] = 'mock';
        if ($provider !== '' && (in_array($provider, $known, true) || $provider === (string) $old['sms_provider'])) {
            $next['sms_provider'] = $provider;
        }
        $next['sms_template'] = sanitize_textarea_field($in['sms_template'] ?? $old['sms_template']);
        $next['email_subject'] = sanitize_text_field($in['email_subject'] ?? $old['email_subject']);
        $next['email_body'] = sanitize_textarea_field($in['email_body'] ?? $old['email_body']);
    }
    return $next;
}

function bha_image_url($key, $fallback) {
    $settings = bha_settings();
    $id = absint($settings[$key] ?? 0);
    if ($id) {
        $url = wp_get_attachment_image_url($id, 'large');
        if ($url) {
            return $url;
        }
    }
    $bundled = [
        'img_fridge' => 'fridge.webp',
        'img_washer' => 'washer.webp',
        'img_coffee' => 'coffee.webp',
        'img_fryer' => 'fryer.webp',
        'img_mobile' => 'fridge.webp',
    ];
    if (isset($bundled[$key])) {
        return BHA_URL . 'assets/products/' . $bundled[$key];
    }
    $path = get_theme_file_path($fallback);
    if ($path && file_exists($path)) {
        return get_theme_file_uri($fallback);
    }
    return '';
}

add_action('admin_menu', function () {
    add_menu_page('ورود و عضویت', 'بهاران هوم', 'manage_options', 'bha-auth', 'bha_settings_page', 'dashicons-shield', 56);
    add_submenu_page('bha-auth', 'ورود و عضویت', 'ورود و عضویت', 'manage_options', 'bha-auth', 'bha_settings_page');
});

add_action('admin_init', function () {
    register_setting('bha_group', 'bha_settings', [
        'sanitize_callback' => 'bha_sanitize_settings',
        'default' => bha_default_settings(),
    ]);
});

function bha_admin_tabs() {
    return [
        'design' => 'طراحی',
        'motion' => 'موشن',
        'auth' => 'احراز هویت',
        'delivery' => 'پیامک و ایمیل',
        'security' => 'امنیت',
        'preview' => 'پیش‌نمایش',
    ];
}

function bha_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    $tab = sanitize_key($_GET['tab'] ?? 'design');
    $tabs = bha_admin_tabs();
    if (!isset($tabs[$tab])) {
        $tab = 'design';
    }
    $s = bha_settings();
    $notice = isset($_GET['bha_notice']) ? sanitize_key(wp_unslash($_GET['bha_notice'])) : '';
    echo '<div class="wrap bha-admin" dir="rtl">';
    echo '<h1>بهاران هوم · ورود و عضویت</h1>';
    echo '<p class="bha-admin-lead">هویت بصری، موشن، روش‌های ورود و اتصال پیامک از همین‌جا کنترل می‌شود. کدهای یک‌بارمصرف در این صفحه نمایش داده نمی‌شوند.</p>';
    if ($notice === 'email-ok') {
        echo '<div class="notice notice-success"><p>نامه آزمایشی به ایمیل مدیر فرستاده شد.</p></div>';
    } elseif ($notice === 'email-fail') {
        echo '<div class="notice notice-error"><p>ارسال نامه آزمایشی انجام نشد. تنظیم نامه سرور را بررسی کنید.</p></div>';
    } elseif ($notice === 'sms-ready') {
        echo '<div class="notice notice-success"><p>سرویس پیامک انتخاب‌شده آماده است. از این دکمه پیامک واقعی و کد ورود فرستاده نمی‌شود.</p></div>';
    } elseif ($notice === 'sms-missing') {
        echo '<div class="notice notice-warning"><p>سرویس پیامک انتخاب‌شده آماده نیست. همان سرویس را در افزونه‌اش تنظیم کنید، یا از این صفحه سرویس دیگری را انتخاب کنید.</p></div>';
    } elseif ($notice === 'sms-plugin') {
        echo '<div class="notice notice-info"><p>ارسال را افزونه انتخاب‌شده انجام می‌دهد. آمادگی آن را در خود همان افزونه بررسی کنید.</p></div>';
    } elseif ($notice === 'mfa-on') {
        echo '<div class="notice notice-success"><p>تأیید دومرحله‌ای این حساب مدیریت فعال شد.</p></div>';
    } elseif ($notice === 'mfa-bad') {
        echo '<div class="notice notice-error"><p>کد تأیید دومرحله‌ای پذیرفته نشد.</p></div>';
    } elseif ($notice === 'mfa-off') {
        echo '<div class="notice notice-success"><p>تأیید دومرحله‌ای این حساب خاموش شد.</p></div>';
    }
    echo '<nav class="bha-tabs" aria-label="بخش‌های تنظیمات">';
    foreach ($tabs as $id => $label) {
        $url = admin_url('admin.php?page=bha-auth&tab=' . $id);
        echo '<a class="' . ($tab === $id ? 'is-active' : '') . '" href="' . esc_url($url) . '">' . esc_html($label) . '</a>';
    }
    echo '</nav>';
    if ($tab === 'security') {
        bha_security_tab();
    } elseif ($tab === 'preview') {
        bha_preview_tab();
    } else {
        echo '<form method="post" action="options.php">';
        settings_fields('bha_group');
        echo '<input type="hidden" name="bha_settings[tab]" value="' . esc_attr($tab) . '">';
        echo '<table class="form-table" role="presentation">';
        if ($tab === 'design') {
            bha_field_color('primary', 'رنگ اصلی', $s['primary']);
            bha_field_color('secondary', 'رنگ ثانویه', $s['secondary']);
            bha_field_color('navy', 'سرمه‌ای', $s['navy']);
            bha_field_color('ice', 'پس‌زمینه', $s['ice']);
            bha_field_color('light', 'آبی روشن', $s['light']);
            bha_field_color('text', 'متن', $s['text']);
            bha_field_color('border', 'حاشیه', $s['border']);
            echo '<tr><th scope="row"><label for="bha-radius">شعاع گوشه‌ها</label></th><td><input id="bha-radius" type="number" min="16" max="36" name="bha_settings[radius]" value="' . esc_attr($s['radius']) . '"> پیکسل</td></tr>';
            bha_field_text('welcome', 'متن خوش‌آمدگویی', $s['welcome']);
            bha_field_text('lead', 'توضیح کوتاه', $s['lead']);
            bha_field_text('button', 'متن دکمه', $s['button']);
            bha_field_media('img_fridge', 'یخچال', $s['img_fridge']);
            bha_field_media('img_washer', 'ماشین لباسشویی', $s['img_washer']);
            bha_field_media('img_coffee', 'قهوه‌ساز', $s['img_coffee']);
            bha_field_media('img_fryer', 'سرخ‌کن', $s['img_fryer']);
            bha_field_media('img_mobile', 'تصویر موبایل', $s['img_mobile']);
        } elseif ($tab === 'motion') {
            bha_field_check('motion', 'موشن ورود صفحه', $s['motion']);
            bha_field_check('float', 'حرکت شناور محصولات', $s['float']);
            bha_field_check('parallax', 'جابه‌جایی محدود با ماوس', $s['parallax']);
            bha_field_check('button_fx', 'واکنش دکمه', $s['button_fx']);
            echo '<tr><th scope="row"><label for="bha-ms">زمان انیمیشن ورود</label></th><td><input id="bha-ms" type="number" min="400" max="900" name="bha_settings[motion_ms]" value="' . esc_attr($s['motion_ms']) . '"> میلی‌ثانیه</td></tr>';
            echo '<tr><th scope="row">حرکت کمتر</th><td><p>اگر سیستم کاربر Reduced Motion را روشن کرده باشد، شناوری، پارالاکس و انیمیشن ورود خاموش می‌شوند.</p></td></tr>';
        } elseif ($tab === 'auth') {
            bha_field_check('mobile_login', 'ورود با موبایل', $s['mobile_login']);
            bha_field_check('email_login', 'ورود با ایمیل', $s['email_login']);
            bha_field_check('password_login', 'ورود با رمز عبور', $s['password_login']);
            bha_field_check('allow_register', 'ساخت حساب مشتری جدید پس از تأیید مالکیت', $s['allow_register']);
            echo '<tr><th scope="row"><label for="bha-ttl">اعتبار کد</label></th><td><input id="bha-ttl" type="number" min="60" max="600" name="bha_settings[otp_ttl]" value="' . esc_attr($s['otp_ttl']) . '"> ثانیه</td></tr>';
            echo '<tr><th scope="row"><label for="bha-resend">فاصله ارسال مجدد</label></th><td><input id="bha-resend" type="number" min="30" max="300" name="bha_settings[resend]" value="' . esc_attr($s['resend']) . '"> ثانیه</td></tr>';
            echo '<tr><th scope="row"><label for="bha-tries">محدودیت تلاش هر کد</label></th><td><input id="bha-tries" type="number" min="3" max="8" name="bha_settings[max_tries]" value="' . esc_attr($s['max_tries']) . '"></td></tr>';
            echo '<tr><th scope="row"><label for="bha-send-limit">سقف ارسال در هر ساعت</label></th><td><input id="bha-send-limit" type="number" min="3" max="20" name="bha_settings[send_limit]" value="' . esc_attr($s['send_limit']) . '"> برای هر اتصال</td></tr>';
            echo '<tr><th scope="row"><label for="bha-session">مدت نشست مشتری</label></th><td><input id="bha-session" type="number" min="1" max="30" name="bha_settings[session_days]" value="' . esc_attr($s['session_days']) . '"> روز، وقتی «مرا به خاطر بسپار» روشن باشد. نشست مدیریت جدا می‌ماند.</td></tr>';
            echo '<tr><th scope="row"><label for="bha-redirect">مسیر پس از ورود</label></th><td><input id="bha-redirect" class="regular-text" type="url" name="bha_settings[redirect]" value="' . esc_attr($s['redirect']) . '" placeholder="' . esc_attr(home_url('/my-account/')) . '"><p class="description">فقط نشانی همین سایت پذیرفته می‌شود. اگر مشتری از تسویه‌حساب آمده باشد، به تسویه‌حساب برمی‌گردد.</p></td></tr>';
            echo '<tr><th scope="row"><label for="bha-surface">صفحه ورود مشتری</label></th><td><select id="bha-surface" name="bha_settings[login_surface]">';
            echo '<option value="baharaan"' . selected($s['login_surface'], 'baharaan', false) . '>فرم بهاران هوم</option>';
            echo '<option value="external"' . selected($s['login_surface'], 'external', false) . '>افزونه یا فرم دیگر</option>';
            echo '</select><p class="description">اگر افزونه دیگری صفحه ورود را می‌سازد، گزینه دوم را بگذارید. در آن حالت این افزونه فرم، عنوان و ظاهر صفحه حساب را عوض نمی‌کند و همان افزونه انتخاب‌شده نشان داده می‌شود.</p></td></tr>';
        } else {
            echo '<tr><th scope="row"><label for="bha-provider">سرویس پیامک</label></th><td><select id="bha-provider" name="bha_settings[sms_provider]">';
            $providers = bha_sms_providers();
            $current = (string) $s['sms_provider'];
            if ($current !== 'mock' && !isset($providers[$current])) {
                echo '<option value="' . esc_attr($current) . '" selected>سرویس ذخیره‌شده («' . esc_html($current) . '») الان فعال نیست</option>';
            }
            foreach ($providers as $id => $provider) {
                $label = is_array($provider) && !empty($provider['label']) ? (string) $provider['label'] : $id;
                echo '<option value="' . esc_attr($id) . '"' . selected($current, $id, false) . '>' . esc_html($label) . '</option>';
            }
            echo '<option value="mock"' . selected($current, 'mock', false) . '>آزمایش محلی بدون ارسال واقعی</option>';
            echo '</select><p class="description">هر سرویسی که اینجا انتخاب شود همان برای کد ورود استفاده می‌شود و به‌صورت خودکار به api.ir برنمی‌گردد. افزونه دیگر می‌تواند با فیلتر bha_sms_providers خودش را به این فهرست اضافه کند.</p></td></tr>';
            echo '<tr><th scope="row"><label for="bha-sms-template">قالب پیامک</label></th><td><textarea id="bha-sms-template" class="large-text" rows="3" name="bha_settings[sms_template]">' . esc_textarea($s['sms_template']) . '</textarea><p class="description">api.ir قالب شماره‌دار خودش را می‌فرستد. برای سرویس یا افزونه دیگری که انتخاب کنید، متن با {code} به همان سرویس داده می‌شود.</p></td></tr>';
            bha_field_text('email_subject', 'موضوع ایمیل', $s['email_subject']);
            echo '<tr><th scope="row"><label for="bha-email-body">متن ایمیل</label></th><td><textarea id="bha-email-body" class="large-text" rows="5" name="bha_settings[email_body]">' . esc_textarea($s['email_body']) . '</textarea><p class="description">{code} و {minutes}</p></td></tr>';
        }
        echo '</table>';
        submit_button('ذخیره تنظیمات');
        echo '</form>';
        if ($tab === 'delivery') {
            echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '" style="margin-top:12px">';
            echo '<input type="hidden" name="action" value="bha_test_delivery">';
            wp_nonce_field('bha_test_delivery');
            submit_button('آزمایش اتصال پیامک', 'secondary', 'sms', false);
            echo ' ';
            submit_button('آزمایش ارسال ایمیل به مدیر', 'secondary', 'email', false);
            echo '</form>';
        }
    }
    echo '</div>';
}

function bha_field_color($key, $label, $value) {
    echo '<tr><th scope="row"><label for="bha-' . esc_attr($key) . '">' . esc_html($label) . '</label></th><td><input id="bha-' . esc_attr($key) . '" type="text" class="bha-color" name="bha_settings[' . esc_attr($key) . ']" value="' . esc_attr($value) . '" pattern="^#[0-9A-Fa-f]{6}$"></td></tr>';
}

function bha_field_text($key, $label, $value) {
    echo '<tr><th scope="row"><label for="bha-' . esc_attr($key) . '">' . esc_html($label) . '</label></th><td><input id="bha-' . esc_attr($key) . '" class="regular-text" type="text" name="bha_settings[' . esc_attr($key) . ']" value="' . esc_attr($value) . '"></td></tr>';
}

function bha_field_check($key, $label, $value) {
    echo '<tr><th scope="row">' . esc_html($label) . '</th><td><label><input type="checkbox" name="bha_settings[' . esc_attr($key) . ']" value="1"' . checked($value, 1, false) . '> فعال</label></td></tr>';
}

function bha_field_media($key, $label, $id) {
    $url = $id ? wp_get_attachment_image_url($id, 'thumbnail') : '';
    echo '<tr><th scope="row">' . esc_html($label) . '</th><td class="bha-media">';
    echo '<input type="hidden" name="bha_settings[' . esc_attr($key) . ']" value="' . esc_attr($id) . '">';
    echo '<img alt="" src="' . esc_url($url) . '"' . ($url ? '' : ' hidden') . '>';
    echo '<button type="button" class="button bha-media-pick">انتخاب تصویر</button> ';
    echo '<button type="button" class="button bha-media-clear">حذف</button>';
    echo '<p class="description">اگر خالی بماند، تصویر استودیویی موجود قالب استفاده می‌شود.</p></td></tr>';
}

function bha_security_tab() {
    $user = wp_get_current_user();
    $enabled = (bool) get_user_meta($user->ID, '_bha_totp_enabled', true);
    $pending = get_transient('bha_totp_pending_' . $user->ID);
    echo '<div class="bha-security-grid">';
    echo '<section><h2>تأیید دومرحله‌ای مدیریت</h2><p>این لایه فقط برای حساب‌های مدیر است و روی ورود مشتریان فروشگاه اثر نمی‌گذارد. Passkey در این نسخه پیاده‌سازی نشده است.</p>';
    if ($enabled) {
        echo '<p><strong>وضعیت:</strong> برای این حساب فعال است.</p>';
        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
        echo '<input type="hidden" name="action" value="bha_mfa_disable">';
        wp_nonce_field('bha_mfa_disable');
        submit_button('خاموش کردن MFA این حساب', 'delete', 'submit', false);
        echo '</form>';
    } else {
        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
        echo '<input type="hidden" name="action" value="bha_mfa_begin">';
        wp_nonce_field('bha_mfa_begin');
        submit_button($pending ? 'ساخت کلید تازه' : 'ساخت کلید MFA', 'secondary', 'submit', false);
        echo '</form>';
        if (is_string($pending) && $pending !== '') {
            $issuer = rawurlencode('Baharaan Home');
            $label = rawurlencode($user->user_login);
            $uri = 'otpauth://totp/' . $issuer . ':' . $label . '?secret=' . rawurlencode($pending) . '&issuer=' . $issuer . '&period=30&digits=6';
            echo '<p>کلید را در برنامه تأیید هویت وارد کنید. این مقدار فقط تا تأیید همین حساب نشان داده می‌شود.</p>';
            echo '<p><code>' . esc_html($pending) . '</code></p>';
            echo '<p><a href="' . esc_attr($uri) . '">افزودن به برنامه تأیید هویت</a></p>';
            echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
            echo '<input type="hidden" name="action" value="bha_mfa_confirm">';
            wp_nonce_field('bha_mfa_confirm');
            echo '<p><label>کد ۶ رقمی <input name="code" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" required></label></p>';
            submit_button('فعال‌سازی پس از تأیید کد');
            echo '</form>';
        }
    }
    echo '</section>';
    echo '<section><h2>رویدادهای اخیر</h2><p>نشانی‌ها به‌صورت درهم ذخیره شده‌اند و کد یا رمز در گزارش نیست.</p><table class="widefat striped"><thead><tr><th>زمان</th><th>رویداد</th><th>اتصال</th></tr></thead><tbody>';
    $log = get_option('bha_security_log', []);
    if (!is_array($log) || !$log) {
        echo '<tr><td colspan="3">هنوز رویدادی ثبت نشده است.</td></tr>';
    } else {
        foreach (array_slice($log, 0, 20) as $row) {
            echo '<tr><td>' . esc_html(wp_date('Y-m-d H:i', (int) ($row['t'] ?? 0))) . '</td><td>' . esc_html((string) ($row['e'] ?? '')) . '</td><td><code>' . esc_html((string) ($row['ip'] ?? '')) . '</code></td></tr>';
        }
    }
    echo '</tbody></table></section></div>';
    echo '<section><h2>سیاست‌ها</h2><ul class="bha-policy">';
    echo '<li>کد یک‌بارمصرف فقط به‌صورت درهم ذخیره می‌شود و پس از مصرف یا انقضا حذف می‌شود.</li>';
    echo '<li>حساب مدیر، مدیر فروشگاه یا ویرایشگر با کد مشتری وارد نمی‌شود.</li>';
    echo '<li>اگر یک شماره به چند حساب وصل باشد، ورود خودکار انجام نمی‌شود.</li>';
    echo '<li>مقصد بعد از ورود فقط به نشانی داخلی مجاز برمی‌گردد.</li>';
    echo '<li>بازیابی رمز از سازوکار استاندارد وردپرس استفاده می‌کند و وجود حساب را اعلام نمی‌کند.</li>';
    echo '</ul></section>';
}

function bha_preview_tab() {
    echo '<p>پیش‌نمایش از همان نشانه‌های طراحی ذخیره شده ساخته می‌شود. رفتار ورود در این قاب اجرا نمی‌شود.</p>';
    echo '<div class="bha-preview-desktop"><div class="bha-preview-label">دسکتاپ</div>';
    bha_render_auth_shell(['preview' => true]);
    echo '</div>';
    echo '<div class="bha-preview-mobile"><div class="bha-preview-label">موبایل</div>';
    bha_render_auth_shell(['preview' => true, 'mobile' => true]);
    echo '</div>';
}

add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook !== 'toplevel_page_bha-auth') {
        return;
    }
    wp_enqueue_media();
    wp_enqueue_style('bha-admin', BHA_URL . 'assets/admin.css', [], BHA_VERSION);
    wp_enqueue_style('bha-auth', BHA_URL . 'assets/auth.css', [], BHA_VERSION);
    wp_enqueue_script('bha-admin', BHA_URL . 'assets/admin.js', ['jquery'], BHA_VERSION, true);
});

add_action('admin_post_bha_test_delivery', function () {
    if (!current_user_can('manage_options')) {
        wp_die('دسترسی ندارید.', 403);
    }
    check_admin_referer('bha_test_delivery');
    $notice = 'sms-missing';
    if (isset($_POST['email'])) {
        $sent = wp_mail(get_option('admin_email'), 'آزمایش نامه بهاران هوم', "این نامه آزمایشی است و کد ورود ندارد.\n");
        $notice = $sent ? 'email-ok' : 'email-fail';
        bha_log($sent ? 'email_test_ok' : 'email_test_fail');
    } else {
        $settings = bha_settings();
        $provider = (string) $settings['sms_provider'];
        if ($provider === 'mock') {
            $notice = 'sms-ready';
        } else {
            $providers = bha_sms_providers();
            $ready = $providers[$provider]['ready'] ?? null;
            if (!isset($providers[$provider])) {
                $notice = 'sms-missing';
            } elseif (!is_callable($ready)) {
                $notice = 'sms-plugin';
            } else {
                $notice = call_user_func($ready) ? 'sms-ready' : 'sms-missing';
            }
        }
        bha_log($notice === 'sms-missing' ? 'sms_test_missing' : 'sms_test_ready');
    }
    wp_safe_redirect(admin_url('admin.php?page=bha-auth&tab=delivery&bha_notice=' . $notice));
    exit;
});

add_action('admin_post_bha_mfa_begin', function () {
    if (!current_user_can('manage_options')) {
        wp_die('دسترسی ندارید.', 403);
    }
    check_admin_referer('bha_mfa_begin');
    $secret = bha_totp_secret();
    set_transient('bha_totp_pending_' . get_current_user_id(), $secret, 15 * MINUTE_IN_SECONDS);
    bha_log('mfa_secret_created');
    wp_safe_redirect(admin_url('admin.php?page=bha-auth&tab=security'));
    exit;
});

add_action('admin_post_bha_mfa_confirm', function () {
    if (!current_user_can('manage_options')) {
        wp_die('دسترسی ندارید.', 403);
    }
    check_admin_referer('bha_mfa_confirm');
    $uid = get_current_user_id();
    $secret = get_transient('bha_totp_pending_' . $uid);
    $code = isset($_POST['code']) ? wp_unslash($_POST['code']) : '';
    if (!is_string($secret) || !bha_totp_verify($secret, $code)) {
        bha_log('mfa_confirm_fail');
        wp_safe_redirect(admin_url('admin.php?page=bha-auth&tab=security&bha_notice=mfa-bad'));
        exit;
    }
    update_user_meta($uid, '_bha_totp_secret', $secret);
    update_user_meta($uid, '_bha_totp_enabled', 1);
    delete_transient('bha_totp_pending_' . $uid);
    bha_log('mfa_enabled');
    wp_safe_redirect(admin_url('admin.php?page=bha-auth&tab=security&bha_notice=mfa-on'));
    exit;
});

add_action('admin_post_bha_mfa_disable', function () {
    if (!current_user_can('manage_options')) {
        wp_die('دسترسی ندارید.', 403);
    }
    check_admin_referer('bha_mfa_disable');
    $uid = get_current_user_id();
    delete_user_meta($uid, '_bha_totp_secret');
    delete_user_meta($uid, '_bha_totp_enabled');
    bha_log('mfa_disabled');
    wp_safe_redirect(admin_url('admin.php?page=bha-auth&tab=security&bha_notice=mfa-off'));
    exit;
});
