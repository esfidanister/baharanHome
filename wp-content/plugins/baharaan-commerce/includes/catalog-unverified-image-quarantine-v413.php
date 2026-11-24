<?php
if (!defined('ABSPATH')) { exit; }

add_action('init', function () {
    if (get_option('bhc_catalog_unverified_image_quarantine_v413_done')) return;

    $q = new WP_Query([
        'post_type' => 'product',
        'post_status' => ['publish', 'draft', 'pending', 'private'],
        'posts_per_page' => -1,
        'fields' => 'ids',
        'no_found_rows' => true,
    ]);
    $detached = [];
    foreach ((array) $q->posts as $id) {
        $state = get_post_meta($id, '_bhc_catalog_quality_state', true);
        if ($state === 'reference_verified') continue;
        $image_status = get_post_meta($id, '_bhc_image_generation_status', true);
        if ($image_status === 'generated_4k_transparent_verified') continue;
        $thumb = get_post_thumbnail_id($id);
        if (!$thumb) continue;
        // Detach only; do not delete the media file because it may be reused in an audit.
        delete_post_thumbnail($id);
        update_post_meta($id, '_bhc_release_blocked', 'yes');
        update_post_meta($id, '_bhc_image_generation_status', 'blocked_until_exact_model');
        update_post_meta($id, '_bhc_image_block_reason', 'تصویر قبلی برای همین مدل به‌طور مستقل تأیید نشده بود؛ تا تطبیق مدل، تصویر از محصول جدا شد.');
        $detached[] = ['product_id' => (int) $id, 'attachment_id' => (int) $thumb];
    }
    update_option('bhc_catalog_unverified_image_quarantine_v413_done', [
        'detached' => count($detached),
        'items' => $detached,
        'at' => current_time('mysql'),
    ], false);
});
