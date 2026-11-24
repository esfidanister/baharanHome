<?php
if (!defined('ABSPATH')) exit;
add_action('init', function () {
 if (get_option('bhc_image_attachment_batch_v57') || !function_exists('wc_get_product_id_by_sku')) return;
 require_once ABSPATH.'wp-admin/includes/image.php';
 $sku='PAKSHOMA-E53W';$id=wc_get_product_id_by_sku($sku);$file=ABSPATH.'wp-content/uploads/bhc-image-references/PAKSHOMA-E53W-generated-4k.png';$attached=0;
 if($id&&is_readable($file)){update_post_meta($id,'_bhc_image_reference_url','https://www.technolife.com/image/gallery-0-TLP-128273_b7e32ac1-7443-4810-8eab-c96ac6c9940c.png');update_post_meta($id,'_bhc_image_reference_evidence','مرجع تصویری دقیق E53-W از فروشگاه تکنولایف با صفحه رسمی پاکشوما برای مدل، ظاهر، پنل، رنگ و ابعاد تطبیق شد؛ واترمارک مرجع در تصویر نهایی حذف شده است.');update_post_meta($id,'_bhc_image_reference_checked_at','2026-11-19');$aid=get_post_thumbnail_id($id);if(!$aid){$aid=wp_insert_attachment(['post_mime_type'=>'image/png','post_title'=>'Pakshoma E53W 4K transparent product image','post_status'=>'inherit','post_parent'=>$id],$file,$id);if($aid&&!is_wp_error($aid)){wp_update_attachment_metadata($aid,['width'=>3743,'height'=>4096,'file'=>'bhc-image-references/PAKSHOMA-E53W-generated-4k.png','sizes'=>[]]);set_post_thumbnail($id,$aid);$attached=1;}}update_post_meta($id,'_bhc_image_generation_status','product_specific_generated');update_post_meta($id,'_bhc_image_status','generated_4k_transparent');update_post_meta($id,'_bhc_image_generated_at','2026-11-19');update_post_meta($id,'_bhc_verification_status','needs_editor_review');update_post_meta($id,'_bhc_catalog_quality_state','reference_needs_verification');}
 update_option('bhc_image_attachment_batch_v57',['attached'=>$attached,'at'=>current_time('mysql')],false);
},57);
