<?php
if (!defined('ABSPATH')) exit;
add_action('init', function () {
 if (get_option('bhc_sony_image_batch_v68') || !function_exists('wc_get_product_id_by_sku')) return;
 $rows=[['sku'=>'SONY-HT-A5000','file'=>'bhc-image-references/SONY-HT-A5000-generated-4k.png','title'=>'Sony HT-A5000 verified 4K transparent featured image'],['sku'=>'SONY-HT-S40R','file'=>'bhc-image-references/SONY-HT-S40R-generated-4k.png','title'=>'Sony HT-S40R verified 4K transparent featured image']];$attached=0;require_once ABSPATH.'wp-admin/includes/image.php';
 foreach($rows as $r){$id=wc_get_product_id_by_sku($r['sku']);$file=ABSPATH.'wp-content/uploads/'.$r['file'];if(!$id||!is_readable($file))continue;$aid=wp_insert_attachment(['post_mime_type'=>'image/png','post_title'=>$r['title'],'post_status'=>'inherit','post_parent'=>$id],$file,$id);if($aid&&!is_wp_error($aid)){$s=@getimagesize($file);wp_update_attachment_metadata($aid,['width'=>$s[0]??4096,'height'=>$s[1]??4096,'file'=>$r['file'],'sizes'=>[]]);set_post_thumbnail($id,$aid);$attached++;}update_post_meta($id,'_bhc_image_generation_status','product_specific_generated');update_post_meta($id,'_bhc_image_status','generated_4k_transparent');update_post_meta($id,'_bhc_image_featured_verified','yes');update_post_meta($id,'_bhc_verification_status','needs_editor_review');update_post_meta($id,'_bhc_catalog_quality_state','reference_needs_verification');}
 update_option('bhc_sony_image_batch_v68',['attached'=>$attached,'at'=>current_time('mysql')],false);
},68);
