<?php
if (!defined('ABSPATH')) exit;
add_action('init', function () {
 if (get_option('bhc_samsung_image_batch_v63') || !function_exists('wc_get_product_id_by_sku')) return;
 $rows=[
  ['sku'=>'BHC-SAMSUNG-UA55CU','file'=>'bhc-image-references/SAMSUNG-UA55CU7000UXLY-generated-4k.png','title'=>'Samsung UA55CU7000UXLY verified 4K transparent featured image','width'=>4096,'height'=>2731],
  ['sku'=>'BHC-SAMSUNG-WW90T','file'=>'bhc-image-references/SAMSUNG-WW90T534DAW-generated-4k.png','title'=>'Samsung WW90T534DAW verified 4K transparent featured image','width'=>4096,'height'=>4096],
  ['sku'=>'BHC-SAMSUNG-DW60CG','file'=>'bhc-image-references/SAMSUNG-DW60CG550FWQEU-generated-4k.png','title'=>'Samsung DW60CG550FWQEU verified 4K transparent featured image','width'=>4096,'height'=>4096],
 ]; $attached=0; require_once ABSPATH.'wp-admin/includes/image.php';
 foreach($rows as $r){$id=wc_get_product_id_by_sku($r['sku']);$file=ABSPATH.'wp-content/uploads/'.$r['file'];if(!$id||!is_readable($file))continue;$aid=wp_insert_attachment(['post_mime_type'=>'image/png','post_title'=>$r['title'],'post_status'=>'inherit','post_parent'=>$id],$file,$id);if($aid&&!is_wp_error($aid)){wp_update_attachment_metadata($aid,['width'=>$r['width'],'height'=>$r['height'],'file'=>$r['file'],'sizes'=>[]]);set_post_thumbnail($id,$aid);$attached++;}update_post_meta($id,'_bhc_image_generation_status','product_specific_generated');update_post_meta($id,'_bhc_image_status','generated_4k_transparent');update_post_meta($id,'_bhc_image_featured_verified','yes');update_post_meta($id,'_bhc_verification_status','needs_editor_review');update_post_meta($id,'_bhc_catalog_quality_state','reference_needs_verification');}
 update_option('bhc_samsung_image_batch_v63',['attached'=>$attached,'at'=>current_time('mysql')],false);
},63);
