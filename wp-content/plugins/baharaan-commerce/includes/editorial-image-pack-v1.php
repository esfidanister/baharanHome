<?php
if (!defined('ABSPATH')) exit;

/* Give each editorial card a related catalog image instead of repeating the
 * generic journal fallback. This is a one-time, reversible metadata migration:
 * it only assigns an existing product thumbnail to an article that has none. */
add_action('init', function () {
    if (get_option('bhc_editorial_image_pack_v1_done')) return;

    $posts = get_posts([
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => 100,
        'meta_key' => '_bhb_editorial_review',
        'meta_value' => 'approved',
        'orderby' => 'date',
        'order' => 'DESC',
    ]);
    $used = [];
    $assigned = 0;
    $keywords = [
        'یخچال' => 'یخچال', 'ظرفشویی' => 'ظرفشویی', 'کولر' => 'کولر',
        'جاروبرقی' => 'جاروبرقی', 'مایکروویو' => 'مایکروویو', 'فر توکار' => 'فر',
        'تلویزیون' => 'تلویزیون', 'تصفیه هوا' => 'تصفیه', 'اتو' => 'اتو',
        'سرخ‌کن' => 'سرخ‌کن', 'لباسشویی' => 'لباسشویی', 'هود' => 'هود',
        'پنکه' => 'پنکه', 'تهویه' => 'تهویه', 'لوازم خانگی' => 'لوازم',
    ];

    foreach ($posts as $post) {
        if (has_post_thumbnail($post->ID)) continue;
        $title = wp_strip_all_tags($post->post_title);
        $term = '';
        foreach ($keywords as $needle => $value) {
            if (mb_stripos($title, $needle, 0, 'UTF-8') !== false) { $term = $value; break; }
        }
        $args = [
            'post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => 30,
            'meta_key' => '_thumbnail_id', 'orderby' => 'date', 'order' => 'DESC',
            'fields' => 'ids',
        ];
        if ($term) $args['s'] = $term;
        $products = get_posts($args);
        if (!$products && $term) { unset($args['s']); $products = get_posts($args); }
        $attachment = 0;
        foreach ($products as $product_id) {
            $candidate = (int)get_post_thumbnail_id($product_id);
            if ($candidate && empty($used[$candidate])) { $attachment = $candidate; break; }
        }
        if (!$attachment) continue;
        set_post_thumbnail($post->ID, $attachment);
        update_post_meta($post->ID, '_bhc_editorial_image_source', 'catalog_product_thumbnail');
        update_post_meta($post->ID, '_bhc_editorial_image_assigned_at', current_time('mysql'));
        $used[$attachment] = true;
        $assigned++;
    }
    update_option('bhc_editorial_image_pack_v1_done', ['assigned' => $assigned, 'at' => current_time('mysql')], false);
}, 31);
