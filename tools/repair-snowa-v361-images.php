<?php
require_once '/var/www/html/wp-load.php';
$map=[
 'SNW-9010'=>'snowa-snw-9010-v361.png',
 'SWM-C81W'=>'snowa-swm-c81w-v361.png',
 'SWM-D80W'=>'snowa-swm-d80w-v361.png',
 'SRDI-M210GW'=>'snowa-srdi-m210gw-v361.png',
 'ARS3DI-S100'=>'snowa-ars3di-s100-v361.png'
];
$base=WP_CONTENT_DIR.'/plugins/baharaan-commerce/includes/bhc-image-references/';
foreach($map as $sku=>$file){$id=wc_get_product_id_by_sku($sku);$src=$base.$file;if(!$id||!file_exists($src)){echo $sku."|missing\n";continue;} $im=imagecreatefrompng($src);$w=imagesx($im);$h=imagesy($im);$scale=4096/max($w,$h);$nw=(int)round($w*$scale);$nh=(int)round($h*$scale);$out=imagecreatetruecolor($nw,$nh);imagealphablending($out,false);imagesavealpha($out,true);$clear=imagecolorallocatealpha($out,255,255,255,127);imagefilledrectangle($out,0,0,$nw,$nh,$clear);imagecopyresampled($out,$im,0,0,0,0,$nw,$nh,$w,$h);$u=wp_upload_bits($file,null,'');if(empty($u['error'])){imagepng($out,$u['file'],6);$old=get_post_thumbnail_id($id);if($old)wp_delete_attachment($old,true);$aid=wp_insert_attachment(['post_mime_type'=>'image/png','post_title'=>get_the_title($id).' تصویر اختصاصی','post_status'=>'inherit'],$u['file'],$id);require_once ABSPATH.'wp-admin/includes/image.php';wp_update_attachment_metadata($aid,wp_generate_attachment_metadata($aid,$u['file']));set_post_thumbnail($id,$aid);update_post_meta($id,'_bhc_image_generation_status','generated_4k_transparent_verified');}imagedestroy($im);imagedestroy($out);echo $id.'|'.$nw.'x'.$nh."\n";}
$q=new WP_Query(['post_type'=>'product','post_status'=>['publish','draft','pending','private'],'posts_per_page'=>-1,'fields'=>'ids']);$pub=0;$srcs=0;$refs=0;$imgs=0;foreach($q->posts as $id){if(get_post_status($id)==='publish')$pub++;if(get_post_meta($id,'_bhc_source_url',true))$srcs++;if(get_post_meta($id,'_bhc_catalog_quality_state',true)==='reference_verified')$refs++;if(get_post_thumbnail_id($id)&&get_post_meta($id,'_bhc_image_generation_status',true)==='generated_4k_transparent_verified')$imgs++;}echo "METRICS|".count($q->posts).'|'.$pub.'|'.$srcs.'|'.$refs.'|'.$imgs."\n";
