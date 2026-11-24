<?php
require_once '/var/www/html/wp-load.php';

$ids = get_posts(['post_type'=>'product','post_status'=>['publish','draft','pending','private'],'posts_per_page'=>-1,'fields'=>'ids']);
$eligible=[]; $skipped=[];
foreach($ids as $id){
    $state=get_post_meta($id,'_bhc_image_generation_status',true);
    if($state!=='product_specific_generated') continue;
    $aid=get_post_thumbnail_id($id); $file=$aid?get_attached_file($aid):'';
    if(!$file || !is_file($file)){ $skipped[]=$id.'|missing-file'; continue; }
    $info=@getimagesize($file); if(!$info){$skipped[]=$id.'|unreadable';continue;}
    $edge=max($info[0],$info[1]);
    if($edge!==4096){$skipped[]=$id.'|edge='.$edge;continue;}
    $im=null; $type=$info[2]??0;
    if($type===IMAGETYPE_PNG && function_exists('imagecreatefrompng')) $im=@imagecreatefrompng($file);
    elseif($type===IMAGETYPE_WEBP && function_exists('imagecreatefromwebp')) $im=@imagecreatefromwebp($file);
    if(!$im){$skipped[]=$id.'|no-gd-decoder';continue;}
    $w=imagesx($im);$h=imagesy($im);$transparent=0;$sampled=0;
    for($y=0;$y<$h;$y+=max(1,(int)floor($h/32))) for($x=0;$x<$w;$x+=max(1,(int)floor($w/32))){
        $rgba=imagecolorat($im,$x,$y); $alpha=($rgba>>24)&0x7F; $transparent += $alpha>0 ? 1 : 0; $sampled++;
    }
    imagedestroy($im);
    if($sampled===0 || $transparent<1){$skipped[]=$id.'|no-alpha';continue;}
    $eligible[]=$id;
}
foreach($eligible as $id){
    update_post_meta($id,'_bhc_image_generation_status','generated_4k_transparent_verified');
    update_post_meta($id,'_bhc_image_long_edge',4096);
    update_post_meta($id,'_bhc_image_checked_at','2026-11-21');
}
echo 'PROMOTED|'.count($eligible)."\n";
echo 'SKIPPED|'.count($skipped)."\n";
foreach(array_slice($skipped,0,20) as $x) echo $x."\n";
