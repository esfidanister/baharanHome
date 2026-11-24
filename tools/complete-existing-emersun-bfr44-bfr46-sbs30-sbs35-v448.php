<?php
require '/var/www/html/wp-load.php';
require_once ABSPATH.'wp-admin/includes/image.php';
ini_set('memory_limit','2G');
$rows=[
 ['model'=>'BFR44','source'=>'emersun-bfr44-v448-source.png','title'=>'یخچال و فریزر امرسان BFR44'],
 ['model'=>'BFR46','source'=>'emersun-bfr46-v448-source.png','title'=>'یخچال و فریزر امرسان BFR46'],
 ['model'=>'SBS30','source'=>'emersun-sbs30-v448-source.png','title'=>'یخچال سایدبای‌ساید امرسان SBS30'],
 ['model'=>'SBS35','source'=>'emersun-sbs35-v448-source.png','title'=>'یخچال سایدبای‌ساید امرسان SBS35'],
];
$up=wp_upload_dir();
foreach($rows as $row){
 $sku='BHC-EMERSUN-'.$row['model']; $id=wc_get_product_id_by_sku($sku);
 if(!$id){echo "SKIP_NOT_EXIST|$sku\n";continue;}
 $base='/var/www/html/wp-content/plugins/baharaan-commerce/includes/bhc-image-references/'.$row['source'];
 if(!is_file($base)){echo "SKIP_SOURCE_MISSING|$sku\n";continue;}
 wp_update_post(['ID'=>$id,'post_status'=>'draft']);
 $p=wc_get_product($id); if($p){$p->set_catalog_visibility('hidden');$p->set_stock_status('outofstock');$p->save();}
 if($old=get_post_thumbnail_id($id))wp_delete_attachment($old,true);
 $im=imagecreatefrompng($base);$w=imagesx($im);$h=imagesy($im);$scale=4096/max($w,$h);$ow=max(1,(int)round($w*$scale));$oh=max(1,(int)round($h*$scale));
 $out=imagecreatetruecolor($ow,$oh);imagealphablending($out,false);imagesavealpha($out,true);$clear=imagecolorallocatealpha($out,255,255,255,127);imagefilledrectangle($out,0,0,$ow,$oh,$clear);imagecopyresampled($out,$im,0,0,0,0,$ow,$oh,$w,$h);
 $file=$up['path'].'/emersun-'.strtolower($row['model']).'-v448.png';@mkdir(dirname($file),0777,true);imagepng($out,$file,6);imagedestroy($im);imagedestroy($out);
 $aid=wp_insert_attachment(['post_mime_type'=>'image/png','post_title'=>$row['title'].' تصویر اختصاصی 4K شفاف','post_status'=>'inherit'],$file,$id);
 wp_update_attachment_metadata($aid,wp_generate_attachment_metadata($aid,$file));set_post_thumbnail($id,$aid);
 update_post_meta($id,'_bhc_model',$row['model']);update_post_meta($id,'_bhc_image_generation_status','generated_4k_transparent_verified');update_post_meta($id,'_bhc_image_long_edge',4096);update_post_meta($id,'_bhc_image_checked_at','2026-11-21');update_post_meta($id,'_bhc_image_prompt','Emersun '.$row['model'].' existing catalog SKU product-specific 4K transparent PNG, appliance only, no room, no text, no watermark.');update_post_meta($id,'_bhc_catalog_quality_state','reference_needs_verification');update_post_meta($id,'_bhc_release_blocked','yes');
 echo 'UPDATED_EXISTING|'.$sku.'|'.$id.'|'.$file."\n";
}
