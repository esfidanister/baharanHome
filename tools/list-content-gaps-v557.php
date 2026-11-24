<?php
require_once '/var/www/html/wp-load.php';
$ids=get_posts(['post_type'=>'product','post_status'=>['publish','draft','pending','private'],'posts_per_page'=>-1,'fields'=>'ids']);
foreach($ids as $id){$c=(string)get_post_field('post_content',$id);$m=[];foreach(['درباره محصول','اطلاعات فنی','وضعیت کاتالوگ'] as $h)if(mb_stripos($c,$h)===false)$m[]=$h;if($m){$p=wc_get_product($id);echo $id.'|'.$p->get_sku().'|'.implode(',',$m)."\n";}}
