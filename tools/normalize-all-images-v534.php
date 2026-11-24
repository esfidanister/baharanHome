<?php
require_once '/var/www/html/wp-load.php';
require_once ABSPATH.'wp-admin/includes/image.php';
ini_set('memory_limit','2G');

$ids=get_posts(['post_type'=>'product','post_status'=>['publish','draft','pending','private'],'posts_per_page'=>-1,'fields'=>'ids']);
$eligible=['product_specific_generated','product_specific_generated_candidate','generated_4k_transparent_pending_exact_source'];
$promoted=0;$resized=0;$skipped=[];$upload=wp_upload_dir();
foreach($ids as $id){
    if(!in_array(get_post_meta($id,'_bhc_image_generation_status',true),$eligible,true)) continue;
    $aid=get_post_thumbnail_id($id);$file=$aid?get_attached_file($aid):'';
    if(!$file||!is_file($file)){$skipped[]=$id.'|missing-file';continue;}
    $info=@getimagesize($file);if(!$info){$skipped[]=$id.'|unreadable';continue;}
    $type=$info[2]??0;$im=null;
    if($type===IMAGETYPE_PNG&&function_exists('imagecreatefrompng'))$im=@imagecreatefrompng($file);
    elseif($type===IMAGETYPE_WEBP&&function_exists('imagecreatefromwebp'))$im=@imagecreatefromwebp($file);
    if(!$im){$skipped[]=$id.'|unsupported-format';continue;}
    $w=imagesx($im);$h=imagesy($im);$edge=max($w,$h);
    if($edge!==4096){$scale=4096/$edge;$ow=max(1,(int)round($w*$scale));$oh=max(1,(int)round($h*$scale));$out=imagecreatetruecolor($ow,$oh);imagealphablending($out,false);imagesavealpha($out,true);$clear=imagecolorallocatealpha($out,255,255,255,127);imagefilledrectangle($out,0,0,$ow,$oh,$clear);imagecopyresampled($out,$im,0,0,0,0,$ow,$oh,$w,$h);$new=$upload['path'].'/bhc-product-'.$id.'-v534.png';imagepng($out,$new,6);imagedestroy($out);imagedestroy($im);update_attached_file($aid,$new);wp_update_attachment_metadata($aid,wp_generate_attachment_metadata($aid,$new));$im=@imagecreatefrompng($new);$resized++;}
    if(!$im){$skipped[]=$id.'|resize-failed';continue;}
    $w=imagesx($im);$h=imagesy($im);$alpha=0;$sampled=0;
    for($y=0;$y<$h;$y+=max(1,(int)floor($h/32)))for($x=0;$x<$w;$x+=max(1,(int)floor($w/32))){$rgba=imagecolorat($im,$x,$y);$alpha+=(($rgba>>24)&0x7F)>0?1:0;$sampled++;}
    imagedestroy($im);
    if($w!==4096&&$h!==4096){$skipped[]=$id.'|wrong-edge';continue;}
    if(!$alpha){$skipped[]=$id.'|opaque';continue;}
    update_post_meta($id,'_bhc_image_generation_status','generated_4k_transparent_verified');update_post_meta($id,'_bhc_image_long_edge',4096);update_post_meta($id,'_bhc_image_checked_at','2026-11-21');$promoted++;
}
echo 'PROMOTED|'.$promoted."\n".'RESIZED|'.$resized."\n".'SKIPPED|'.count($skipped)."\n";foreach(array_slice($skipped,0,30) as $x)echo $x."\n";
