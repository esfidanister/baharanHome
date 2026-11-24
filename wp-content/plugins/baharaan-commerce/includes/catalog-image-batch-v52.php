<?php
if (!defined('ABSPATH')) exit;
add_action('init', function () {
 if (get_option('bhc_image_attachment_batch_v52') || !function_exists('wc_get_product_id_by_sku')) return;
 require_once ABSPATH.'wp-admin/includes/image.php';
 $rows = [
  ['sku'=>'LG-F2Y1VYP6J','file'=>'bhc-image-references/LG-F2Y1VYP6J-generated-4k.png','title'=>'LG F2Y1VYP6J 4K transparent product image','width'=>3053,'height'=>4096],
  ['sku'=>'LG-OLED55B46LA','file'=>'bhc-image-references/LG-OLED55B46LA-generated-4k.png','title'=>'LG OLED55B46LA 4K transparent product image','width'=>4096,'height'=>2716],
 ];
 $attached = 0;
 foreach ($rows as $row) {
  $product_id = wc_get_product_id_by_sku($row['sku']); $absolute = ABSPATH.'wp-content/uploads/'.$row['file'];
  if (!$product_id || !is_readable($absolute)) continue;
  $attachment_id = get_post_thumbnail_id($product_id);
  if (!$attachment_id) {
   $attachment_id = wp_insert_attachment(['post_mime_type'=>'image/png','post_title'=>$row['title'],'post_status'=>'inherit','post_parent'=>$product_id],$absolute,$product_id);
   if ($attachment_id && !is_wp_error($attachment_id)) { wp_update_attachment_metadata($attachment_id,['width'=>$row['width'],'height'=>$row['height'],'file'=>$row['file'],'sizes'=>[]]); set_post_thumbnail($product_id,$attachment_id); $attached++; }
  }
  update_post_meta($product_id,'_bhc_image_generation_status','product_specific_generated'); update_post_meta($product_id,'_bhc_image_status','generated_4k_transparent'); update_post_meta($product_id,'_bhc_image_generated_at','2026-11-19'); update_post_meta($product_id,'_bhc_verification_status','needs_editor_review'); update_post_meta($product_id,'_bhc_catalog_quality_state','reference_needs_verification');
 }
 update_option('bhc_image_attachment_batch_v52',['attached'=>$attached,'at'=>current_time('mysql')],false);
}, 52);
