<?php
require_once '/var/www/html/wp-load.php';$ids=get_posts(['post_type'=>'product','post_status'=>['publish','draft','pending','private'],'posts_per_page'=>-1,'fields'=>'ids']);$n=0;
foreach($ids as $id){if(get_post_meta($id,'_bhc_release_state',true)!=='ready_for_release')continue;$p=wc_get_product($id);if(!$p)continue;$p->set_status('publish');$p->set_catalog_visibility('visible');$p->set_stock_status('outofstock');$p->save();$n++;}
echo "PUBLISHED_READY|$n\n";
