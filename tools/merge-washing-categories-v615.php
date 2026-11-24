<?php
require_once '/var/www/html/wp-load.php';
$canonical=18;$alias=get_term_by('slug','شستشو','product_cat');
if(!$alias){echo "ALIAS_MISSING\n";exit;}
if((int)$alias->term_id===$canonical){echo "ALREADY_CANONICAL\n";exit;}
$ids=get_posts(['post_type'=>'product','post_status'=>'any','posts_per_page'=>-1,'fields'=>'ids','tax_query'=>[['taxonomy'=>'product_cat','field'=>'term_id','terms'=>[(int)$alias->term_id]]]]);
$moved=0;
foreach($ids as $id){$r=wp_set_object_terms($id,[$canonical],'product_cat',true);if(!is_wp_error($r)){wp_remove_object_terms($id,(int)$alias->term_id,'product_cat');$moved++;}}
$deleted=wp_delete_term((int)$alias->term_id,'product_cat');
echo 'MERGED|ALIAS|'.$alias->term_id.'|CANONICAL|'.$canonical.'|MOVED|'.$moved.'|DELETED|'.($deleted?'1':'0')."\n";
