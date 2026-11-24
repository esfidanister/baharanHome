<?php
if (!defined('ABSPATH')) { exit; }
add_action('init', function () {
    if (get_option('bhc_sony_kd75x75k_quarantine_v408_done')) return;
    $id = 482;
    if (get_post($id)) {
        $thumb = get_post_thumbnail_id($id);
        if ($thumb) { wp_delete_attachment($thumb, true); delete_post_thumbnail($id); }
        update_post_meta($id, '_bhc_catalog_quality_state', 'reference_invalid');
        update_post_meta($id, '_bhc_verification_status', 'invalid_reference');
        update_post_meta($id, '_bhc_release_blocked', 'yes');
        update_post_meta($id, '_bhc_image_generation_status', 'blocked_until_exact_model');
        update_post_meta($id, '_bhc_image_block_reason', 'صفحه رسمی خانواده Sony X75K اندازه ۷۵ اینچ را تأیید نمی‌کند؛ مشخصات مدل‌های نزدیک به این رکورد منتقل نمی‌شود.');
        update_post_meta($id, '_bhc_source_type', 'official_family_contradicts_model');
        update_post_meta($id, '_bhc_source_checked_at', '2026-11-20');
        update_post_meta($id, '_bhc_verification_note', 'منبع رسمی Sony X75K فقط KD-43X75K، KD-50X75K، KD-55X75K و KD-65X75K را فهرست می‌کند؛ KD-75X75K در منبع رسمی خانواده وجود ندارد.');
    }
    update_option('bhc_sony_kd75x75k_quarantine_v408_done', ['quarantined'=>[$id], 'at'=>current_time('mysql')], false);
});
