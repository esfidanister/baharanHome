<?php
require_once '/var/www/html/wp-load.php';
$ids=get_posts(['post_type'=>'product','post_status'=>['publish','draft','pending','private'],'posts_per_page'=>-1,'fields'=>'ids','orderby'=>'ID','order'=>'ASC']);
$demo=0;$specific=0;$missing=0;$status=[];
foreach($ids as $id){
  $p=wc_get_product($id); if(!$p)continue;
  $html=$p->get_image('bh-product-card');
  $is_demo=strpos($html,'bhb-demo-image')!==false;
  $has_img=(bool)$p->get_image_id();
  $st=(string)get_post_meta($id,'_bhc_image_generation_status',true);
  $status[$st]=($status[$st]??0)+1;
  if($is_demo)$demo++; elseif($has_img)$specific++; else $missing++;
}
echo 'TOTAL|'.count($ids).'|SPECIFIC='.$specific.'|DEMO='.$demo.'|MISSING='.$missing."\n";
foreach($status as $k=>$v)echo 'STATUS|'.($k!==''?$k:'EMPTY').'|'.$v."\n";
