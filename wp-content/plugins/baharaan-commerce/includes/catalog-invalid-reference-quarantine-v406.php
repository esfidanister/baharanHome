<?php
if (!defined('ABSPATH')) { exit; }
add_action('init', function () {
    if (get_option('bhc_catalog_invalid_reference_v406_done')) return;
    $items=[344=>'Samsung Q1460 در منابع رسمی/فروشگاهی معتبر به‌عنوان مدل لباسشویی سامسونگ قابل تطبیق نیست و نتایج جست‌وجو نامرتبط هستند.'];$done=[];
    foreach($items as $id=>$reason){if(!get_post($id))continue;$thumb=get_post_thumbnail_id($id);if($thumb){wp_delete_attachment($thumb,true);delete_post_thumbnail($id);}update_post_meta($id,'_bhc_catalog_quality_state','reference_invalid');update_post_meta($id,'_bhc_verification_status','invalid_reference');update_post_meta($id,'_bhc_release_blocked','yes');update_post_meta($id,'_bhc_image_generation_status','blocked_until_valid_model');update_post_meta($id,'_bhc_image_block_reason',$reason);update_post_meta($id,'_bhc_source_type','no_valid_reference_found');$done[]=$id;}
    update_option('bhc_catalog_invalid_reference_v406_done',['quarantined'=>$done,'at'=>current_time('mysql')],false);
});
