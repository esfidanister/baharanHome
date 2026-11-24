<?php
require_once '/var/www/html/wp-load.php';
$ids=get_posts(['post_type'=>'product','post_status'=>['publish','draft','pending','private'],'posts_per_page'=>-1,'fields'=>'ids']);
foreach($ids as $id){$s=get_post_meta($id,'_bhc_image_generation_status',true);if(in_array($s,['generated_4k_transparent_verified'],true))continue;$aid=get_post_thumbnail_id($id);$f=$aid?get_attached_file($aid):'';$i=$f&&is_file($f)?@getimagesize($f):false;$sku=wc_get_product($id)?wc_get_product($id)->get_sku():'';echo $id.'|'.$sku.'|'.$s.'|'.($i?$i[0].'x'.$i[1]:'missing')."\n";}
