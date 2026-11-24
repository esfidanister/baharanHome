<?php
if (!defined('ABSPATH')) exit;
add_action('init', function () {
 if (get_option('bhc_image_attachment_batch_v55') || !function_exists('wc_get_product_id_by_sku')) return;
 require_once ABSPATH.'wp-admin/includes/image.php';
 $rows=[
  ['sku'=>'LG-OLED77B46LA','file'=>'bhc-image-references/LG-OLED77B46LA-generated-4k.png','title'=>'LG OLED77B46LA 4K transparent product image','width'=>4096,'height'=>2305],
  ['sku'=>'LG-OLED65G46LA','file'=>'bhc-image-references/LG-OLED65G46LA-generated-4k.png','title'=>'LG OLED65G46LA 4K transparent product image','width'=>4096,'height'=>2305],
  ['sku'=>'LG-86QNED86T6A','file'=>'bhc-image-references/LG-86QNED86T6A-generated-4k.png','title'=>'LG 86QNED86T6A 4K transparent product image','width'=>4096,'height'=>2716],
  ['sku'=>'LG-A9K-CORE2','file'=>'bhc-image-references/LG-A9K-CORE2-generated-4k.png','title'=>'LG A9K-CORE2 4K transparent product image','width'=>2731,'height'=>4096],
 ];
 $attached=0;
 foreach($rows as $row){$id=wc_get_product_id_by_sku($row['sku']);$file=ABSPATH.'wp-content/uploads/'.$row['file'];if(!$id||!is_readable($file))continue;$aid=get_post_thumbnail_id($id);if(!$aid){$aid=wp_insert_attachment(['post_mime_type'=>'image/png','post_title'=>$row['title'],'post_status'=>'inherit','post_parent'=>$id],$file,$id);if($aid&&!is_wp_error($aid)){wp_update_attachment_metadata($aid,['width'=>$row['width'],'height'=>$row['height'],'file'=>$row['file'],'sizes'=>[]]);set_post_thumbnail($id,$aid);$attached++;}}update_post_meta($id,'_bhc_image_generation_status','product_specific_generated');update_post_meta($id,'_bhc_image_status','generated_4k_transparent');update_post_meta($id,'_bhc_image_generated_at','2026-11-19');update_post_meta($id,'_bhc_verification_status','needs_editor_review');update_post_meta($id,'_bhc_catalog_quality_state','reference_needs_verification');}
 update_option('bhc_image_attachment_batch_v55',['attached'=>$attached,'at'=>current_time('mysql')],false);
},55);
