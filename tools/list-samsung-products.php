<?php
require '/var/www/html/wp-load.php';
$ids=get_posts(['post_type'=>'product','post_status'=>['publish','draft','pending','private'],'numberposts'=>-1,'fields'=>'ids','orderby'=>'ID','order'=>'ASC']);
foreach($ids as $id){$p=wc_get_product($id);$brands=wp_get_post_terms($id,'pa_brand',['fields'=>'names']);$t=get_the_title($id);if(stripos($t,'سامسونگ')===false&&!in_array('Samsung',$brands,true)&&!in_array('سامسونگ',$brands,true))continue;echo $id.'|'.$p->get_status().'|'.$p->get_sku().'|'.implode(',',$brands).'|'.$t.'|'.(has_post_thumbnail($id)?'IMG':'NOIMG').'|'.get_post_meta($id,'_bhc_catalog_quality_state',true).PHP_EOL;}
