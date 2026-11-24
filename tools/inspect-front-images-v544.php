<?php
require_once '/var/www/html/wp-load.php';
$qs=['SLD-43SA1260','UA8500','SLD-55SA1260UL'];
foreach($qs as $q){$ids=get_posts(['post_type'=>'product','post_status'=>['publish','draft','pending','private'],'posts_per_page'=>10,'s'=>$q,'fields'=>'ids']);echo "QUERY|$q\n";foreach($ids as $id){$p=wc_get_product($id);$aid=get_post_thumbnail_id($id);$file=$aid?get_attached_file($aid):'';echo 'PRODUCT|'.$id.'|'.$p->get_name().'|SKU='.$p->get_sku().'|THUMB='.$aid.'|URL='.get_the_post_thumbnail_url($id,'bh-product-card').'|FILE='.$file."\n";}}
