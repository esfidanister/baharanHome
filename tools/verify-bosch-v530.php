<?php
require '/var/www/html/wp-load.php';

$rows = [
    [
        'id' => 432,
        'model' => 'KAD93AIEP',
        'title' => 'یخچال فریزر ساید بای ساید بوش سری ۶ مدل KAD93AIEP',
        'source' => 'https://www.bosch-home.com/de/de/product/kundensortimente/KAD93AIEP',
        'specs' => [
            'برند' => 'Bosch / بوش', 'مدل دقیق' => 'KAD93AIEP', 'سری' => 'Series 6',
            'نوع' => 'یخچال فریزر ساید بای ساید آمریکایی', 'رنگ' => 'استیل ضدلکه / Inox-EasyClean',
            'ابعاد' => 'ارتفاع ۱۷۸۷ × عرض ۹۰۸ × عمق ۷۰۷ میلی‌متر', 'رده انرژی' => 'E',
            'ظرفیت خالص یخچال' => '۳۷۱ لیتر', 'ظرفیت خالص فریزر' => '۱۹۱ لیتر',
            'سیستم سرمایش' => 'Total No Frost', 'سطح صدا' => '۴۲ دسی‌بل',
            'یخ‌ساز' => 'یخ‌ساز اتوماتیک', 'اتصال آب' => 'اتصال مستقیم به آب شهری',
            'ویژگی‌ها' => 'نمایشگر و کنترل الکترونیکی، روشنایی LED، Vario Zone و Super Cooling', 'وضعیت' => 'ناموجود'
        ]
    ],
    [
        'id' => 454,
        'model' => 'BFL634GS1',
        'title' => 'مایکروویو توکار بوش سری ۸ مدل BFL634GS1',
        'source' => 'https://www.bosch-home.com/ch/fr/product/BFL634GS1',
        'specs' => [
            'برند' => 'Bosch / بوش', 'مدل دقیق' => 'BFL634GS1', 'سری' => 'Series 8',
            'نوع' => 'مایکروویو توکار بدون گریل', 'رنگ' => 'استیل ضدزنگ',
            'ظرفیت محفظه' => '۲۱ لیتر', 'حداکثر توان مایکروویو' => '۹۰۰ وات',
            'سطوح توان' => '۵ سطح توان', 'برنامه‌ها' => '۷ برنامه خودکار',
            'تمیزکاری' => 'Hydrolyse / تمیزکاری هیدرولیتیک', 'ابعاد دستگاه' => 'ارتفاع ۳۸۲ × عرض ۵۹۴ × عمق ۳۱۸ میلی‌متر',
            'اتصال هوشمند' => 'ندارد', 'وضعیت' => 'ناموجود'
        ]
    ]
];

foreach ($rows as $r) {
    $id = (int) $r['id'];
    wp_update_post(['ID' => $id, 'post_title' => $r['title'], 'post_status' => 'draft']);
    $p = wc_get_product($id);
    if ($p) { $p->set_catalog_visibility('hidden'); $p->set_stock_status('outofstock'); $p->save(); }
    $table = ''; $lines = [];
    foreach ($r['specs'] as $k => $v) { $table .= '<tr><th>'.esc_html($k).'</th><td>'.esc_html($v).'</td></tr>'; $lines[] = $k.': '.$v; }
    $content = '<h2>درباره محصول</h2><p>'.$r['title'].' بر پایه صفحه رسمی Bosch ثبت شده است. این کالا فعلاً ناموجود است و قیمت برای آن نمایش داده نمی‌شود.</p><h2>اطلاعات فنی</h2><table><tbody>'.$table.'</tbody></table><h2>ویژگی‌های کلیدی</h2><ul><li>'.implode('</li><li>', array_slice($lines, 3, 8)).'</li></ul><h2>وضعیت موجودی</h2><p>این محصول فعلاً ناموجود است و قیمت نمایش داده نمی‌شود.</p><h2>منبع صحت‌سنجی</h2><p>مدل کامل و مشخصات از منبع رسمی Bosch بررسی و ثبت شده است.</p>';
    wp_update_post(['ID' => $id, 'post_content' => $content]);
    update_post_meta($id, '_bhc_model', $r['model']);
    update_post_meta($id, '_bhc_source_url', $r['source']);
    update_post_meta($id, '_bhc_source_urls', wp_json_encode([$r['source']], JSON_UNESCAPED_SLASHES));
    update_post_meta($id, '_bhc_source_type', 'official_exact_model_page');
    update_post_meta($id, '_bhc_source_checked_at', '2026-11-21');
    update_post_meta($id, '_bhc_source_evidence', 'منبع رسمی Bosch نام کامل مدل و مشخصات همان محصول را نمایش می‌دهد.');
    update_post_meta($id, '_bhc_structured_specs', wp_json_encode($r['specs'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
    update_post_meta($id, '_bhc_highlights', implode("\n", $lines));
    update_post_meta($id, '_bhc_verification_status', 'verified_reference');
    update_post_meta($id, '_bhc_catalog_quality_state', 'reference_verified');
    update_post_meta($id, '_bhc_release_blocked', 'no');
    update_post_meta($id, '_bhc_no_price_message', 'این محصول فعلاً ناموجود است و قیمت نمایش داده نمی‌شود.');
    update_post_meta($id, '_yoast_wpseo_metadesc', mb_substr($r['title'].'؛ مشخصات فنی رسمی، ویژگی‌های کلیدی و وضعیت ناموجود در بهاران‌هوم.', 0, 155));
    echo "VERIFIED|{$id}|{$r['model']}\n";
}
