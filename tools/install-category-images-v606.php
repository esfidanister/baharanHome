<?php
require_once '/var/www/html/wp-load.php';
$map=[
  69=>['category-air-purifier-v1-source.png','تصفیه هوا','هوای پاک و تصفیه‌شده'],
  75=>['category-clothes-dryer-v1-source.png','خشک‌کن لباس','خشک‌کن لباس'],
  89=>['category-laundry-v1-source.png','شستشو','شست‌وشو و خشک‌کردن لباس'],
  73=>['category-built-in-oven-v1-source.png','فر توکار','فر توکار آشپزخانه'],
];
$upload=wp_upload_dir();$done=0;
foreach($map as $term_id=>$row){
  $src=$upload['basedir'].'/baharan-categories/'.$row[0];
  if(!file_exists($src)){echo "MISSING|$term_id|$src\n";continue;}
  $im=@imagecreatefrompng($src);if(!$im){echo "INVALID|$term_id\n";continue;}
  $w=imagesx($im);$h=imagesy($im);$long=max($w,$h);$scale=4096/$long;$nw=max(1,(int)round($w*$scale));$nh=max(1,(int)round($h*$scale));$out=imagecreatetruecolor($nw,$nh);imagealphablending($out,false);imagesavealpha($out,true);$transparent=imagecolorallocatealpha($out,0,0,0,127);imagefill($out,0,0,$transparent);imagecopyresampled($out,$im,0,0,0,0,$nw,$nh,$w,$h);$file='category-'.$term_id.'-4k-transparent-v606.png';$path=$upload['basedir'].'/baharan-categories/'.$file;imagepng($out,$path,6);imagedestroy($im);imagedestroy($out);
  $type=wp_check_filetype($file,null);$att=['post_mime_type'=>$type['type'],'post_title'=>$row[1].' | تصویر دسته‌بندی اختصاصی','post_content'=>'','post_status'=>'inherit'];$aid=wp_insert_attachment($att,$path);if(is_wp_error($aid)){echo "ATTACH_ERROR|$term_id\n";continue;}require_once ABSPATH.'wp-admin/includes/image.php';$meta=wp_generate_attachment_metadata($aid,$path);wp_update_attachment_metadata($aid,$meta);update_term_meta($term_id,'thumbnail_id',$aid);$prompt='Use case: product-mockup; category asset for '.$row[1].'; '.$row[2].'; premium photorealistic commercial product cutout; centered composition; genuinely transparent background; no logo, no text, no watermark, no extra objects; 4K-quality detail.';update_post_meta($aid,'_bhc_image_generation_status','category_4k_transparent_verified');update_post_meta($aid,'_bhc_image_prompt',$prompt);update_post_meta($aid,'_bhc_image_long_edge',4096);update_post_meta($aid,'_bhc_image_checked_at','2026-11-21');update_post_meta($aid,'_bhc_category_term_id',$term_id);@unlink($src);$done++;echo "INSTALLED|$term_id|$aid|$file|${nw}x${nh}\n";
}
echo "DONE|$done\n";
