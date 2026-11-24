<?php
require_once '/var/www/html/wp-load.php';
$ids=[1007,28,1317,482];
foreach($ids as $id){
  $p=wc_get_product($id);
  if(!$p){echo "MISSING|$id\n";continue;}
  echo "PRODUCT|$id|".$p->get_name()."|thumb=".get_post_thumbnail_id($id)."\n";
  echo "META|source=".get_post_meta($id,'_bhc_source_url',true)."|image_status=".get_post_meta($id,'_bhc_image_generation_status',true)."|verified=".get_post_meta($id,'_bhc_reference_verified',true)."\n";
  echo "GETIMAGE|".substr(preg_replace('/\s+/',' ', $p->get_image('bh-product-card',['loading'=>'lazy'])),0,900)."\n";
  echo "THUMBURL|".get_the_post_thumbnail_url($id,'bh-product-card')."\n";
}
echo "TEMPLATE|".wc_locate_template('content-product.php')."\n";
echo "FILTERS|thumbnail=".(has_filter('woocommerce_get_product_thumbnail')?'yes':'no')."|postthumb=".(has_filter('post_thumbnail_html')?'yes':'no')."\n";
