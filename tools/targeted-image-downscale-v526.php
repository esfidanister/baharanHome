<?php
require '/var/www/html/wp-load.php';
ini_set('memory_limit', '2G'); set_time_limit(0);
$q=new WP_Query(['post_type'=>'product','post_status'=>['publish','draft','pending','private'],'posts_per_page'=>-1,'fields'=>'ids']);
$done=0;
foreach($q->posts as $pid){
  $aid=get_post_thumbnail_id($pid); if(!$aid)continue; $file=get_attached_file($aid); if(!$file||!is_file($file))continue;
  $actual=@getimagesize($file); $w=(int)($actual[0]??0); $h=(int)($actual[1]??0); if(max($w,$h)<=4096)continue;
  $mime=function_exists('mime_content_type')?@mime_content_type($file):get_post_mime_type($aid); $im=($mime==='image/webp'&&function_exists('imagecreatefromwebp'))?@imagecreatefromwebp($file):@imagecreatefrompng($file); if(!$im)continue;
  $scale=4096/max(imagesx($im),imagesy($im)); $nw=max(1,(int)round(imagesx($im)*$scale)); $nh=max(1,(int)round(imagesy($im)*$scale));
  $out=imagecreatetruecolor($nw,$nh); imagealphablending($out,false); imagesavealpha($out,true); $clear=imagecolorallocatealpha($out,255,255,255,127); imagefilledrectangle($out,0,0,$nw,$nh,$clear); imagecopyresampled($out,$im,0,0,0,0,$nw,$nh,imagesx($im),imagesy($im));
  $target=preg_replace('/\.[^.]+$/','.png',$file); if(imagepng($out,$target,6)){ if($target!==$file)@unlink($file); update_attached_file($aid,$target); wp_update_post(['ID'=>$aid,'post_mime_type'=>'image/png']); wp_update_attachment_metadata($aid,['width'=>$nw,'height'=>$nh,'file'=>basename($target),'sizes'=>[]]); update_post_meta($pid,'_bhc_image_long_edge',4096); $done++; }
  imagedestroy($im); imagedestroy($out);
}
echo "TARGETED_DOWNSCALE|$done\n";
