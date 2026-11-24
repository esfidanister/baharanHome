<?php
if (!defined('ABSPATH')) { exit; }
add_action('init', function () {
    if (get_option('bhc_catalog_family_quarantine_v403_done')) return;
    $ids=[339,344,378,379,380,381,382,384,385,386,391,392,393,394,395,396,397,398,409,410,411,412,413,414,415,416,474,476,477,478,482];
    $done=[];
    foreach($ids as $id){
        if(!get_post($id)) continue;
        $state=get_post_meta($id,'_bhc_catalog_quality_state',true);
        if($state==='reference_verified') continue;
        $thumb=get_post_thumbnail_id($id);
        if($thumb){ wp_delete_attachment($thumb,true); delete_post_thumbnail($id); }
        update_post_meta($id,'_bhc_catalog_quality_state','reference_needs_verification');
        update_post_meta($id,'_bhc_verification_status','unverified');
        update_post_meta($id,'_bhc_release_blocked','yes');
        update_post_meta($id,'_bhc_image_generation_status','blocked_until_exact_model');
        update_post_meta($id,'_bhc_image_block_reason','کد کامل مدل/پسوند منطقه‌ای مشخص نیست؛ تصویر اختصاصی پس از تطبیق منبع رسمی ساخته می‌شود.');
        update_post_meta($id,'_bhc_source_type','family_reference_not_exact');
        $done[]=$id;
    }
    update_option('bhc_catalog_family_quarantine_v403_done',['quarantined'=>$done,'at'=>current_time('mysql')],false);
});
