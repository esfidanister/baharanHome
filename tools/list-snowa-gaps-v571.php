<?php
require_once '/var/www/html/wp-load.php';
$ids=get_posts(['post_type'=>'product','post_status'=>['publish','draft','pending','private'],'posts_per_page'=>-1,'fields'=>'ids']);
foreach($ids as $id){$p=wc_get_product($id);if(!$p)continue;$b=wp_get_post_terms($id,'pa_brand',['fields'=>'names']);if(($b[0]??'')==='اسنوا'&&get_post_meta($id,'_bhc_catalog_quality_state',true)==='reference_needs_verification')echo $id.'|'.$p->get_sku().'|'.get_post_meta($id,'_bhc_model',true).'|'.$p->get_name().PHP_EOL;}
