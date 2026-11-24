<?php
/**
 * پیشخوان اجرای سرویس‌های رسمی api.ir. فقط مدیر؛ ورودی‌ها ذخیره نمی‌شوند.
 */
if (!defined('ABSPATH')) {
    exit;
}

function bhc_apiir_groups() {
    return [
        'link' => 'اتصال و ابزار',
        'message' => 'پیامک، تماس و هوش مصنوعی',
        'identity' => 'هویت و احراز',
        'bank' => 'بانک و شبا',
        'cheque' => 'چک و تسهیلات',
        'company' => 'شرکت، مالیات و مجوز',
        'vehicle' => 'خودرو، گواهینامه و نظام وظیفه',
        'place' => 'آدرس، پست، قبض و اتباع',
    ];
}

function bhc_apiir_group_for($file) {
    $map = [
        'echo' => 'link', 'my-ip' => 'link', 'ip-location' => 'link', 'ip-is-iran' => 'link', 'is-holiday' => 'link', 'wallpaper' => 'link', 'check-email' => 'link', 'enamad' => 'link',
        'sms-otp' => 'message', 'send-sms' => 'message', 'call' => 'message', 'call-otp' => 'message', 'call-otp-alt' => 'message', 'text-to-speech' => 'message', 'chat-gpt' => 'message',
        'shahkar' => 'identity', 'shahkar-lite' => 'identity', 'shahkar-pro' => 'identity', 'person-info' => 'identity', 'person-data' => 'identity', 'sana' => 'identity', 'video-verify' => 'identity', 'video-verify-speech-text' => 'identity',
        'card-match' => 'bank', 'card-mobile-match' => 'bank', 'card-info' => 'bank', 'card-to-iban' => 'bank', 'bank-card-info' => 'bank', 'bank-account-info' => 'bank', 'iban-info' => 'bank', 'iban-match' => 'bank', 'iban-match-pro' => 'bank',
        'unpaid-cheque' => 'cheque', 'unpaid-cheque-lite' => 'cheque', 'unpaid-cheque-pro' => 'cheque', 'cheque-color' => 'cheque', 'cheque-info' => 'cheque', 'active-loans' => 'cheque',
        'company-info' => 'company', 'company-members' => 'company', 'company-newspapers' => 'company', 'company-signatories' => 'company', 'tax-records' => 'company', 'license' => 'company', 'medical-license' => 'company',
        'driving-score' => 'vehicle', 'driving-license' => 'vehicle', 'driving-lisense' => 'vehicle', 'military-status' => 'vehicle', 'active-plates' => 'vehicle', 'plate-history' => 'vehicle', 'vehicle-card' => 'vehicle', 'vehicle-info' => 'vehicle', 'vehicle-violation' => 'vehicle', 'passport-status' => 'vehicle',
        'geo-to-address' => 'place', 'postal-code-info' => 'place', 'postal-code-pro' => 'place', 'postal-code-location' => 'place', 'postal-tracking' => 'place', 'watter-bill' => 'place', 'watter-bill-info' => 'place', 'gas-bill' => 'place', 'gas-bill-info' => 'place', 'power-bill' => 'place', 'power-bill-info' => 'place', 'nationality-status' => 'place',
    ];
    return $map[$file] ?? 'link';
}

function bhc_apiir_field_label($name) {
    $labels = [
        'name' => 'نام', 'nationalCode' => 'کد ملی', 'mobile' => 'موبایل', 'isCompany' => 'شخص حقوقی', 'birthDate' => 'تاریخ تولد شمسی',
        'cardNumber' => 'شماره کارت', 'iban' => 'شماره شبا', 'accountNumber' => 'شماره حساب', 'bankCode' => 'کد بانک (اختیاری)',
        'voiceID' => 'شناسه فایل صوتی', 'numbers' => 'شماره‌ها، هر کدام در یک خط', 'code' => 'کد', 'number' => 'شماره',
        'template' => 'قالب پیامک (۰ تا ۴)', 'message' => 'متن پیامک', 'mobiles' => 'موبایل‌ها، هر کدام در یک خط',
        'nationalID' => 'شناسه ملی شرکت', 'inquiryCode' => 'کد استعلام مالیاتی', 'domain' => 'دامنه', 'weekend' => 'آخر هفته هم تعطیل حساب شود',
        'ip' => 'آی‌پی نسخه ۴', 'email' => 'ایمیل', 'latitude' => 'عرض جغرافیایی', 'longitude' => 'طول جغرافیایی',
        'postalCode' => 'کد پستی', 'trackingCode' => 'کد رهگیری', 'billID' => 'شناسه قبض', 'command' => 'دستور',
        'data' => 'متن ورودی', 'temperature' => 'میزان خلاقیت', 'text' => 'متن', 'male' => 'صدای گوینده آقا باشد',
        'ttsEngine' => 'موتور صدا (۱ تا ۳)', 'chequeID' => 'شناسه چک', 'medicalCode' => 'کد نظام پزشکی',
        'licenseNumber' => 'شماره گواهینامه', 'plateNumber' => 'پلاک', 'codeType' => 'نوع کد اتباع (۱ تا ۴)',
        'serialNumber' => 'سریال کارت ملی', 'videoBase64' => 'ویدئو', 'speechText' => 'متن خوانده‌شده',
        'matchingThreshold' => 'آستانه چهره', 'livenessThreshold' => 'آستانه زنده‌سنجی', 'speechThreshold' => 'آستانه گفتار',
    ];
    return $labels[$name] ?? $name;
}

function bhc_apiir_catalog() {
    static $items = null;
    if (is_array($items)) {
        return $items;
    }
    $items = [];
    foreach (glob(dirname(__DIR__) . '/apiir/services/*.php') ?: [] as $path) {
        $src = file_get_contents($path);
        if (!is_string($src) || !preg_match('/function\s+(apiir_[a-z0-9_]+)\s*\(([^)]*)\)/', $src, $fn)) {
            continue;
        }
        $title = 'سرویس';
        if (preg_match('/سرویس\s+\S+\s+—\s+(.+)/u', $src, $heading)) {
            $title = trim($heading[1]);
        }
        $summary = '';
        if (preg_match('/function\s+' . preg_quote($fn[1], '/') . '[\s\S]*?\*\/\s*function/u', $src) === 0 && preg_match('/\*\s+([^\n*@][^\n]*)\s*\n\s+\*\s*\n\s+\*\s+@param/u', $src, $sum)) {
            $summary = trim($sum[1]);
        }
        $fields = [];
        foreach (array_filter(array_map('trim', explode(',', $fn[2]))) as $raw) {
            if (!preg_match('/^\$([A-Za-z_][A-Za-z0-9_]*)(?:\s*=\s*(.+))?$/', $raw, $param)) {
                continue;
            }
            if ($param[1] === 'timeout') {
                continue;
            }
            $default = isset($param[2]) ? trim($param[2]) : null;
            $kind = 'text';
            if ($default === 'true' || $default === 'false' || in_array($param[1], ['isCompany', 'male', 'weekend'], true)) {
                $kind = 'bool';
            } elseif (in_array($param[1], ['numbers', 'mobiles'], true)) {
                $kind = 'lines';
            } elseif (in_array($param[1], ['message', 'data', 'text', 'speechText'], true)) {
                $kind = 'textarea';
            } elseif ($param[1] === 'videoBase64') {
                $kind = 'video';
            } elseif ($default !== null && preg_match('/^-?\d+\.\d+$/', $default)) {
                $kind = 'float';
            } elseif ($default !== null && preg_match('/^-?\d+$/', $default) && $default !== "''") {
                $kind = 'int';
            } elseif (in_array($param[1], ['latitude', 'longitude', 'temperature'], true)) {
                $kind = 'float';
            }
            $fields[] = [
                'name' => $param[1],
                'label' => bhc_apiir_field_label($param[1]),
                'kind' => $kind,
                'default' => $default,
            ];
        }
        $slug = basename($path, '.php');
        $items[$fn[1]] = [
            'fn' => $fn[1],
            'slug' => $slug,
            'title' => $title,
            'summary' => $summary,
            'group' => bhc_apiir_group_for($slug),
            'free' => $fn[1] === 'apiir_echo',
            'fields' => $fields,
        ];
    }
    uasort($items, function ($a, $b) {
        return strcmp($a['title'], $b['title']);
    });
    return $items;
}

function bhc_apiir_console() {
    if (!current_user_can('manage_options')) {
        return;
    }
    $catalog = bhc_apiir_catalog();
    $groups = bhc_apiir_groups();
    $group = isset($_GET['api_group']) ? sanitize_key(wp_unslash($_GET['api_group'])) : 'link';
    if (!isset($groups[$group])) {
        $group = 'link';
    }
    $open = isset($_GET['api_service']) ? sanitize_key(wp_unslash($_GET['api_service'])) : '';
    $result = get_transient('bhc_apiir_result_' . get_current_user_id());
    if ($result !== false) {
        delete_transient('bhc_apiir_result_' . get_current_user_id());
    }
    echo '<hr><h2>همه سرویس‌ها</h2>';
    echo '<p>هر کارت همان تابع رسمی SDK است. به‌جز Echo، هر اجرای موفق از اعتبار حساب api.ir کم می‌کند. اگر پاسخ ناموفق بود، دوباره ارسال نکنید.</p>';
    echo '<nav class="bhc-apiir-groups">';
    foreach ($groups as $key => $label) {
        $count = count(array_filter($catalog, function ($item) use ($key) { return $item['group'] === $key; }));
        $url = add_query_arg(['page' => 'bhc-apiir', 'api_group' => $key], admin_url('admin.php'));
        echo '<a class="' . ($group === $key ? 'is-active' : '') . '" href="' . esc_url($url) . '">' . esc_html($label) . ' <small>' . intval($count) . '</small></a>';
    }
    echo '</nav>';
    if (is_array($result)) {
        echo '<h3>نتیجه ' . esc_html((string) ($result['title'] ?? '')) . '</h3>';
        echo '<pre class="bhc-apiir-result">' . esc_html((string) ($result['body'] ?? '')) . '</pre>';
    }
    echo '<div class="bhc-apiir-grid">';
    foreach ($catalog as $item) {
        if ($item['group'] !== $group) {
            continue;
        }
        $id = 'bhc-apiir-' . $item['slug'];
        echo '<article class="bhc-apiir-card" id="' . esc_attr($id) . '">';
        echo '<header><b>' . esc_html($item['title']) . '</b><code>' . esc_html($item['fn']) . '</code></header>';
        if ($item['summary'] !== '') {
            echo '<p>' . esc_html($item['summary']) . '</p>';
        }
        if ($item['free']) {
            echo '<p class="bhc-apiir-free">این سرویس رایگان است.</p>';
        }
        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '" enctype="multipart/form-data">';
        echo '<input type="hidden" name="action" value="bhc_apiir_run">';
        echo '<input type="hidden" name="service" value="' . esc_attr($item['fn']) . '">';
        echo '<input type="hidden" name="api_group" value="' . esc_attr($group) . '">';
        wp_nonce_field('bhc_apiir_run_' . $item['fn']);
        foreach ($item['fields'] as $field) {
            $name = 'arg_' . $field['name'];
            echo '<label>' . esc_html($field['label']);
            if ($field['kind'] === 'bool') {
                $checked = $field['default'] === 'true' ? ' checked' : '';
                echo '<input type="checkbox" name="' . esc_attr($name) . '" value="1"' . $checked . '>';
            } elseif ($field['kind'] === 'textarea' || $field['kind'] === 'lines') {
                echo '<textarea name="' . esc_attr($name) . '" rows="3"></textarea>';
            } elseif ($field['kind'] === 'video') {
                echo '<input type="file" name="video" accept="video/*">';
            } elseif ($field['kind'] === 'int') {
                $value = $field['default'] !== null ? preg_replace('/\D/', '', $field['default']) : '';
                echo '<input type="number" name="' . esc_attr($name) . '" value="' . esc_attr((string) $value) . '">';
            } elseif ($field['kind'] === 'float') {
                echo '<input type="text" name="' . esc_attr($name) . '" value="' . esc_attr($field['default'] === null ? '' : $field['default']) . '">';
            } else {
                echo '<input type="text" name="' . esc_attr($name) . '" value="' . esc_attr($field['default'] !== null && $field['default'] !== "''" ? trim($field['default'], "'\"") : '') . '">';
            }
            echo '</label>';
        }
        if (!$item['fields']) {
            echo '<p>این سرویس ورودی ندارد.</p>';
        }
        echo '<button type="submit" class="button button-primary">اجرای سرویس</button></form></article>';
        unset($open);
    }
    echo '</div>';
    echo '<style>.bhc-apiir-groups{display:flex;flex-wrap:wrap;gap:8px;margin:12px 0 18px}.bhc-apiir-groups a{padding:6px 10px;border:1px solid #d0d7de;border-radius:8px;text-decoration:none}.bhc-apiir-groups a.is-active{background:#087f72;color:#fff;border-color:#087f72}.bhc-apiir-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px}.bhc-apiir-card{padding:14px;border:1px solid #dcdcde;border-radius:12px;background:#fff}.bhc-apiir-card header{display:flex;justify-content:space-between;gap:8px;align-items:baseline}.bhc-apiir-card code{font-size:11px}.bhc-apiir-card label{display:block;margin:8px 0;font-weight:600}.bhc-apiir-card input[type=text],.bhc-apiir-card input[type=number],.bhc-apiir-card textarea,.bhc-apiir-card input[type=file]{display:block;width:100%;margin-top:4px;font-weight:400}.bhc-apiir-free{color:#087f72}.bhc-apiir-result{max-height:360px;overflow:auto;padding:12px;background:#0f1720;color:#e8eef6;border-radius:10px;direction:ltr;text-align:left}</style>';
}

function bhc_apiir_run() {
    if (!current_user_can('manage_options')) {
        wp_die('دسترسی مجاز نیست');
    }
    $fn = isset($_POST['service']) ? sanitize_key(wp_unslash($_POST['service'])) : '';
    check_admin_referer('bhc_apiir_run_' . $fn);
    $catalog = bhc_apiir_catalog();
    $group = isset($_POST['api_group']) ? sanitize_key(wp_unslash($_POST['api_group'])) : 'link';
    $back = add_query_arg(['page' => 'bhc-apiir', 'api_group' => $group, 'api_service' => $fn], admin_url('admin.php'));
    if (!isset($catalog[$fn]) || !function_exists($fn)) {
        set_transient('bhc_apiir_result_' . get_current_user_id(), ['title' => $fn, 'body' => 'این سرویس در SDK موجود نیست.'], MINUTE_IN_SECONDS);
        wp_safe_redirect($back);
        exit;
    }
    $item = $catalog[$fn];
    $args = [];
    foreach ($item['fields'] as $field) {
        $raw = wp_unslash($_POST['arg_' . $field['name']] ?? '');
        if ($field['kind'] === 'bool') {
            $args[] = !empty($_POST['arg_' . $field['name']]);
        } elseif ($field['kind'] === 'lines') {
            $lines = preg_split('/\R/u', (string) $raw) ?: [];
            $args[] = array_values(array_filter(array_map('sanitize_text_field', $lines)));
        } elseif ($field['kind'] === 'textarea') {
            $args[] = sanitize_textarea_field((string) $raw);
        } elseif ($field['kind'] === 'video') {
            $file = $_FILES['video'] ?? null;
            if (!is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || !is_uploaded_file((string) ($file['tmp_name'] ?? '')) || (int) $file['size'] > 5 * 1024 * 1024) {
                set_transient('bhc_apiir_result_' . get_current_user_id(), ['title' => $item['title'], 'body' => 'ویدئو باید انتخاب شود و حداکثر ۵ مگابایت باشد.'], MINUTE_IN_SECONDS);
                wp_safe_redirect($back);
                exit;
            }
            $bin = file_get_contents($file['tmp_name']);
            $args[] = $bin === false ? '' : base64_encode($bin);
        } elseif ($field['kind'] === 'int') {
            $args[] = (int) $raw;
        } elseif ($field['kind'] === 'float') {
            $args[] = (float) str_replace(',', '.', (string) $raw);
        } else {
            $args[] = sanitize_text_field((string) $raw);
        }
    }
    try {
        $result = call_user_func_array($fn, $args);
    } catch (Throwable $e) {
        $result = ['success' => false, 'code' => 0, 'message' => $e->getMessage(), 'data' => null];
    }
    $body = wp_json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if (is_string($body) && strlen($body) > 20000) {
        $body = substr($body, 0, 20000) . "\n…";
    }
    set_transient('bhc_apiir_result_' . get_current_user_id(), [
        'title' => $item['title'],
        'body' => is_string($body) ? $body : 'پاسخ قابل نمایش نیست.',
    ], MINUTE_IN_SECONDS);
    wp_safe_redirect($back . '#bhc-apiir-' . $item['slug']);
    exit;
}

add_action('admin_post_bhc_apiir_run', 'bhc_apiir_run');
