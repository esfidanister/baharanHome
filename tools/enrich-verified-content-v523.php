<?php
require '/var/www/html/wp-load.php';

$brandMap = [
    'امرسان' => 'امرسان',
    'سونی' => 'سونی',
    'ال‌جی' => 'ال‌جی',
    'ال جی' => 'ال جی',
];
$seoAdded = 0; $highlightsAdded = 0; $brandsAdded = 0; $verified = 0;
$q = new WP_Query(['post_type'=>'product','post_status'=>['publish','draft','pending','private'],'posts_per_page'=>-1,'fields'=>'ids']);
foreach ($q->posts as $id) {
    if (get_post_meta($id, '_bhc_catalog_quality_state', true) !== 'reference_verified') continue;
    $verified++;
    $title = wp_strip_all_tags(get_the_title($id));

    if (!get_post_meta($id, '_yoast_wpseo_metadesc', true)) {
        $model = get_post_meta($id, '_bhc_model', true);
        $desc = $title . '؛ بررسی مشخصات فنی، ویژگی‌ها و اطلاعات کاتالوگ مدل' . ($model ? ' ' . $model : '') . '. این کالا فعلاً ناموجود است و قیمت در کاتالوگ نمایش داده نمی‌شود.';
        update_post_meta($id, '_yoast_wpseo_metadesc', mb_substr($desc, 0, 155));
        update_post_meta($id, '_bhc_seo_enriched_at', '2026-11-21');
        $seoAdded++;
    }

    if (!get_post_meta($id, '_bhc_highlights', true)) {
        $raw = get_post_meta($id, '_bhc_structured_specs', true);
        $data = json_decode($raw, true);
        $lines = [];
        if (is_array($data)) {
            $preferred = ['برند','مدل دقیق','نوع','ظرفیت','گنجایش کل طبق منبع','سایز','رزولوشن','توان خروجی','رده انرژی','سیستم‌عامل','وضعیت'];
            foreach ($preferred as $key) {
                if (isset($data[$key]) && trim((string)$data[$key]) !== '') $lines[] = $key . ': ' . $data[$key];
                if (count($lines) >= 8) break;
            }
            if (count($lines) < 4) {
                foreach ($data as $key => $value) {
                    if (is_scalar($value) && trim((string)$value) !== '' && !in_array($key, $preferred, true)) $lines[] = $key . ': ' . $value;
                    if (count($lines) >= 8) break;
                }
            }
        }
        if ($lines) {
            update_post_meta($id, '_bhc_highlights', implode("\n", $lines));
            update_post_meta($id, '_bhc_content_enriched_at', '2026-11-21');
            $highlightsAdded++;
        }
    }

    $terms = wp_get_post_terms($id, 'product_brand');
    if (!$terms || is_wp_error($terms) || !count($terms)) {
        foreach ($brandMap as $needle => $termName) {
            if (mb_strpos($title, $needle) === false) continue;
            $term = get_term_by('name', $termName, 'product_brand');
            if (!$term && $needle !== $termName) $term = get_term_by('name', $needle, 'product_brand');
            if ($term && !is_wp_error($term)) {
                wp_set_post_terms($id, [(int)$term->term_id], 'product_brand', false);
                update_post_meta($id, '_bhc_brand_normalized_at', '2026-11-21');
                $brandsAdded++;
            }
            break;
        }
    }
}
echo "CONTENT_V523|verified=$verified|seo_added=$seoAdded|highlights_added=$highlightsAdded|brands_added=$brandsAdded\n";
