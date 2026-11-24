<?php
if (!defined('ABSPATH')) { exit; }

add_action('init', function () {
    if (get_option('bhc_catalog_pakshoma_pvc6020_v412_done')) return;

    $id = 160;
    $reason = 'برای پاکشوما PVC-6020 منبع مستقیمِ قابل اتکا پیدا نشد؛ منابع موجود فقط مدل‌های نزدیک PVC-25501، PVC-25502 و PVC-5125 را نشان می‌دهند و مشخصات یا تصویر آن‌ها قابل انتقال به PVC-6020 نیست.';
    $done = false;
    if (get_post($id)) {
        $thumb = get_post_thumbnail_id($id);
        if ($thumb) {
            wp_delete_attachment($thumb, true);
            delete_post_thumbnail($id);
        }
        update_post_meta($id, '_bhc_catalog_quality_state', 'reference_needs_verification');
        update_post_meta($id, '_bhc_verification_status', 'pending_exact_model');
        update_post_meta($id, '_bhc_release_blocked', 'yes');
        update_post_meta($id, '_bhc_source_type', 'nearby_models_only_exact_match_missing');
        update_post_meta($id, '_bhc_source_gap_reason', $reason);
        update_post_meta($id, '_bhc_source_review_notes', 'تا زمان پیدا شدن منبع دقیق همین مدل، هیچ مشخصه‌ای از مدل‌های نزدیک کپی نشود. تصویر محصول نیز فقط پس از تأیید مدل و ساخت تصویر اختصاصی مجاز است.');
        update_post_meta($id, '_bhc_image_generation_status', 'blocked_until_exact_model');
        update_post_meta($id, '_bhc_image_block_reason', $reason);
        $done = true;
    }
    update_option('bhc_catalog_pakshoma_pvc6020_v412_done', ['product_id'=>$id, 'quarantined'=>$done, 'at'=>current_time('mysql')], false);
});
