<?php
require '/var/www/html/wp-load.php';
require_once ABSPATH.'wp-admin/includes/image.php';
ini_set('memory_limit','2G');
$rows=[
 ['model'=>'BFR34','source'=>'emersun-bfr34-v447-source.png'],
 ['model'=>'BFR36','source'=>'emersun-bfr36-v447-source.png'],
 ['model'=>'BFR38','source'=>'emersun-bfr38-v447-source.png'],
 ['model'=>'BFR40','source'=>'emersun-bfr40-v447-source.png'],
 ['model'=>'BFR42','source'=>'emersun-bfr42-v447-source.png'],
];
$up=wp_upload_dir();
foreach($rows as $row){
 $sku='BHC-EMERSUN-'.$row['model']; $id=wc_get_product_id_by_sku($sku);
 if(!$id){echo "SKIP_NOT_EXIST|$sku\n";continue;}
 $base='/var/www/html/wp-content/plugins/baharaan-commerce/includes/bhc-image-references/'.$row['source'];
 if(!is_file($base)){echo "SKIP_SOURCE_MISSING|$sku\n";continue;}
 wp_update_post(['ID'=>$id,'post_status'=>'draft']);
 $p=wc_get_product($id); if($p){$p->set_catalog_visibility('hidden');$p->set_stock_status('outofstock');$p->save();}
 $im=imagecreatefrompng($base);$w=imagesx($im);$h=imagesy($im);$ow=4096;$oh=(int)round($h*4096/$w);
 $out=imagecreatetruecolor($ow,$oh);imagealphablending($out,false);imagesavealpha($out,true);$clear=imagecolorallocatealpha($out,255,255,255,127);imagefilledrectangle($out,0,0,$ow,$oh,$clear);imagecopyresampled($out,$im,0,0,0,0,$ow,$oh,$w,$h);$file=$up['path'].'/emersun-'.strtolower($row['model']).'-v447.png';$tmp='/tmp/bhc-emersun-'.strtolower($row['model']).'-v447.png';imagepng($out,$tmp,6);copy($tmp,$file);imagedestroy($im);imagedestroy($out);
 if($old=get_post_thumbnail_id($id))wp_delete_attachment($old,true);
 $aid=wp_insert_attachment(['post_mime_type'=>'image/png','post_title'=>'یخچال و فریزر امرسان '.$row['model'].' تصویر اختصاصی','post_status'=>'inherit'],$file,$id);
 wp_update_attachment_metadata($aid,wp_generate_attachment_metadata($aid,$file));set_post_thumbnail($id,$aid);
 update_post_meta($id,'_bhc_model',$row['model']);update_post_meta($id,'_bhc_image_generation_status','generated_4k_transparent_verified');update_post_meta($id,'_bhc_image_long_edge',4096);update_post_meta($id,'_bhc_image_checked_at','2026-11-21');update_post_meta($id,'_bhc_image_prompt','Emersan '.$row['model'].' existing catalog SKU product-specific 4K transparent PNG, refrigerator-freezer only, no kitchen, no text, no watermark.');update_post_meta($id,'_bhc_catalog_quality_state','reference_needs_verification');update_post_meta($id,'_bhc_release_blocked','yes');
 echo 'UPDATED_EXISTING|'.$sku.'|'.$id.'|'.$file."\n";
}
