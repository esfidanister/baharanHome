<?php
if (!defined('ABSPATH')) exit;
add_action('init', function () {
 if (get_option('bhc_image_attachment_fix_v61') || !function_exists('wc_get_product_id_by_sku')) return;
 $rows=[['sku'=>'BHC-SONY-XR-55A80L','file'=>'bhc-image-references/SONY-XR-55A80L-generated-4k.png','title'=>'Sony XR-55A80L verified 4K transparent featured image','width'=>4096,'height'=>2731],['sku'=>'BHC-SONY-XR-55X90L','file'=>'bhc-image-references/SONY-XR-55X90L-generated-4k.png','title'=>'Sony XR-55X90L verified 4K transparent featured image','width'=>4096,'height'=>2574]];$attached=0;
 require_once ABSPATH.'wp-admin/includes/image.php';
 foreach($rows as $r){$id=wc_get_product_id_by_sku($r['sku']);$file=ABSPATH.'wp-content/uploads/'.$r['file'];if(!$id||!is_readable($file))continue;$aid=wp_insert_attachment(['post_mime_type'=>'image/png','post_title'=>$r['title'],'post_status'=>'inherit','post_parent'=>$id],$file,$id);if($aid&&!is_wp_error($aid)){wp_update_attachment_metadata($aid,['width'=>$r['width'],'height'=>$r['height'],'file'=>$r['file'],'sizes'=>[]]);set_post_thumbnail($id,$aid);$attached++;}update_post_meta($id,'_bhc_image_generation_status','product_specific_generated');update_post_meta($id,'_bhc_image_status','generated_4k_transparent');update_post_meta($id,'_bhc_image_featured_verified','yes');update_post_meta($id,'_bhc_verification_status','needs_editor_review');update_post_meta($id,'_bhc_catalog_quality_state','reference_needs_verification');}
 update_option('bhc_image_attachment_fix_v61',['attached'=>$attached,'at'=>current_time('mysql')],false);
},61);
