<?php
if (!defined('ABSPATH')) { exit; }

add_action('init', function () {
    if (get_option('bhc_catalog_tfh18_evidence_correction_v416_done')) return;
    $id = 496;
    if (get_post($id)) {
        $thumb = get_post_thumbnail_id($id);
        if ($thumb) delete_post_thumbnail($id); // Keep media recoverable; detach from the product.
        $specs = [
            'برند' => 'امرسان',
            'مدل دقیق' => 'TFH18',
            'ظرفیت اسمی' => '۱۸ فوت',
            'نوع ساختار' => 'یخچال‌فریزر کشودار / فریزر بالا',
            'وضعیت' => 'ناموجود',
        ];
        update_post_meta($id, '_bhc_specs', $specs);
        update_post_meta($id, '_bhc_structured_specs', wp_json_encode($specs, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
        update_post_meta($id, '_bhc_catalog_quality_state', 'reference_needs_verification');
        update_post_meta($id, '_bhc_verification_status', 'pending_full_exact_specs');
        update_post_meta($id, '_bhc_release_blocked', 'yes');
        update_post_meta($id, '_bhc_image_generation_status', 'blocked_until_exact_model');
        update_post_meta($id, '_bhc_source_gap_reason', 'منبع موجود کد TFH18، ظرفیت ۱۸ فوت و ساختار کشودار را تأیید می‌کند اما برای ابعاد، گنجایش تفکیکی، انرژی، امکانات و تصویر دقیق کافی نیست.');
        update_post_meta($id, '_bhc_source_review_notes', 'تصویر قبلی هوش‌مصنوعی بود و تا دسترسی به مرجع تصویری یا تأیید کامل مدل نباید نمایش داده شود. مشخصات تکمیلی از مدل‌های نزدیک منتقل نشود.');
    }
    update_option('bhc_catalog_tfh18_evidence_correction_v416_done', ['product_id'=>$id, 'at'=>current_time('mysql')], false);
});
