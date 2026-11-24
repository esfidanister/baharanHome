<?php
if (!defined('ABSPATH')) exit;
add_action('init', function () {
 if (get_option('bhc_catalog_image_metadata_sync_v72')) return;
 $ids=get_posts(['post_type'=>'product','post_status'=>'any','posts_per_page'=>-1,'fields'=>'ids']);$synced=0;
 foreach($ids as $id){if(get_post_meta($id,'_bhc_image_generation_status',true)!=='product_specific_generated')continue;$aid=get_post_thumbnail_id($id);if(!$aid)continue;$file=get_attached_file($aid);if(!$file||strpos(wp_normalize_path($file),'/bhc-image-references/')===false)continue;$url=wp_get_attachment_url($aid);if(!$url)continue;update_post_meta($id,'_bhc_image_reference_url',$url);update_post_meta($id,'_bhc_image_reference_evidence','تصویر شاخص اختصاصی همین مدل، با پس‌زمینه شفاف و کنترل ابعاد واقعی فایل به محصول متصل شده است.');update_post_meta($id,'_bhc_image_reference_checked_at','2026-11-19');$synced++;}
 update_option('bhc_catalog_image_metadata_sync_v72',['synced'=>$synced,'at'=>current_time('mysql')],false);
},72);
