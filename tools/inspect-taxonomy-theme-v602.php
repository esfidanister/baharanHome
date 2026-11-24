<?php
require_once '/var/www/html/wp-load.php';
$unc = get_term_by('slug','uncategorized','product_cat');
echo 'UNC_TERM|'.($unc?(int)$unc->term_id:0).'|'.($unc?(int)$unc->count:0).'\n';
if($unc){$ids=get_posts(['post_type'=>'product','post_status'=>'any','posts_per_page'=>-1,'fields'=>'ids','tax_query'=>[['taxonomy'=>'product_cat','field'=>'term_id','terms'=>[$unc->term_id]]]]);echo 'UNC_PRODUCTS|'.count($ids).'\n';foreach($ids as $id){$p=wc_get_product($id);$terms=wp_get_post_terms($id,'product_cat',['fields'=>'names']);echo $id.'|'.$p->get_sku().'|'.$p->get_name().'|'.implode(',',$terms).'\n';}}
echo 'ACTIVE_THEME|'.get_option('stylesheet').'|'.get_option('template').'\n';
$keys=$wpdb->get_col("SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '_wp_global_styles_%'");
foreach($keys as $key){$v=get_option($key);$s=is_string($v)?$v:wp_json_encode($v);json_decode($s,true);echo 'GLOBAL_STYLE|'.$key.'|JSON_ERROR|'.json_last_error_msg().'|LEN|'.strlen($s).'\n';}
$theme_json=ABSPATH.'wp-content/themes/'.get_option('stylesheet').'/theme.json';echo 'THEME_JSON|'.$theme_json.'|'.(file_exists($theme_json)?filesize($theme_json):0).'\n';
