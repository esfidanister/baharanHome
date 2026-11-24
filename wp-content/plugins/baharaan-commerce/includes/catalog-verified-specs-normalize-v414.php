<?php
if (!defined('ABSPATH')) { exit; }

add_action('init', function () {
    if (get_option('bhc_catalog_verified_specs_normalize_v414_done')) return;

    $q = new WP_Query([
        'post_type' => 'product',
        'post_status' => ['publish', 'draft', 'pending', 'private'],
        'posts_per_page' => -1,
        'fields' => 'ids',
        'no_found_rows' => true,
    ]);
    $normalized = 0;
    foreach ((array) $q->posts as $id) {
        if (get_post_meta($id, '_bhc_catalog_quality_state', true) !== 'reference_verified') continue;
        $raw = get_post_meta($id, '_bhc_specs', true);
        if (!is_array($raw) || !$raw) continue;

        if (!get_post_meta($id, '_bhc_structured_specs', true)) {
            update_post_meta($id, '_bhc_structured_specs', wp_json_encode($raw, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }
        if (!get_post_meta($id, '_bhc_highlights', true)) {
            $values = array_values($raw);
            update_post_meta($id, '_bhc_highlights', implode("\n", array_slice($values, 0, 8)));
        }
        if (!get_post_meta($id, '_bhc_no_price_message', true)) {
            update_post_meta($id, '_bhc_no_price_message', 'قیمت نمایش داده نمی‌شود. وضعیت کالا: ناموجود.');
        }

        $content = (string) get_post_field('post_content', $id);
        if (mb_stripos(wp_strip_all_tags($content), 'اطلاعات فنی') === false) {
            $list = '';
            foreach ($raw as $key => $value) {
                if (is_array($value) || is_object($value)) $value = wp_json_encode($value, JSON_UNESCAPED_UNICODE);
                $list .= '<li><strong>' . esc_html($key) . ':</strong> ' . esc_html((string) $value) . '</li>';
            }
            $content .= '<h3>اطلاعات فنی</h3><ul>' . $list . '</ul><p>قیمت درج نشده است و وضعیت فعلی کالا ناموجود است. اطلاعات فقط بر اساس منبع ثبت‌شده همین مدل ارائه شده‌اند.</p>';
            wp_update_post(['ID' => $id, 'post_content' => $content]);
        }
        $normalized++;
    }
    update_option('bhc_catalog_verified_specs_normalize_v414_done', ['normalized' => $normalized, 'at' => current_time('mysql')], false);
});
