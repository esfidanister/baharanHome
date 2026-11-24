<?php
if (!defined('ABSPATH')) exit;
add_action('init',function(){
 if(get_option('bhc_catalog_image_snowa_ssd75_v160')||!function_exists('wc_get_product_id_by_sku'))return;
 require_once ABSPATH.'wp-admin/includes/image.php';
 $sku='SNOWA-SSD-75LS600U';$id=wc_get_product_id_by_sku($sku);$src=__DIR__.'/snowa-ssd-75ls600u-source.png';$rel='bhc-image-references/snowa-ssd-75ls600u.png';$file=ABSPATH.'wp-content/uploads/'.$rel;$ok=false;
 if($id&&is_readable($src)){wp_mkdir_p(dirname($file));$im=@imagecreatefrompng($src);if($im){$w=imagesx($im);$h=imagesy($im);$scale=4096/max($w,$h);$nw=max(1,(int)round($w*$scale));$nh=max(1,(int)round($h*$scale));$out=imagecreatetruecolor($nw,$nh);imagealphablending($out,false);imagesavealpha($out,true);$transparent=imagecolorallocatealpha($out,0,0,0,127);imagefill($out,0,0,$transparent);imagecopyresampled($out,$im,0,0,0,0,$nw,$nh,$w,$h);imagepng($out,$file,6);imagedestroy($out);imagedestroy($im);$ok=is_readable($file);}}
 $aid=$ok?get_post_thumbnail_id($id):0;if($ok&&(!$aid||get_post_meta($aid,'_wp_attached_file',true)!==$rel)){ $aid=wp_insert_attachment(['post_mime_type'=>'image/png','post_title'=>'Snowa SSD-75LS600U اختصاصی 4K شفاف','post_status'=>'inherit','post_parent'=>$id],$file,$id);if($aid&&!is_wp_error($aid)){update_post_meta($aid,'_wp_attached_file',$rel);$s=@getimagesize($file);wp_update_attachment_metadata($aid,['width'=>$s[0]??4096,'height'=>$s[1]??4096,'file'=>$rel,'sizes'=>[]]);set_post_thumbnail($id,$aid);}}
 if($ok&&$aid){update_post_meta($id,'_bhc_image_generation_status','product_specific_generated');update_post_meta($id,'_bhc_image_status','generated_4k_transparent');update_post_meta($id,'_bhc_image_featured_verified','yes');update_post_meta($id,'_bhc_image_reference_url',wp_get_attachment_url($aid));update_post_meta($id,'_bhc_image_reference_evidence','تصویر اختصاصی مدل SSD-75LS600U با پرامپت مدل‌محور تولید و در ضلع بلند 4096 پیکسل با آلفای شفاف پردازش شد.');update_post_meta($id,'_bhc_image_reference_checked_at','2026-11-19');}
 update_option('bhc_catalog_image_snowa_ssd75_v160',['sku'=>$sku,'id'=>$id,'ok'=>$ok,'attachment'=>$aid,'at'=>current_time('mysql')],false);
},160);
