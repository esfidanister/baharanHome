<?php
if (!defined('ABSPATH')) exit;
add_action('init',function(){
    if(get_option('bhc_catalog_taxonomy_hygiene_v150')||!function_exists('wc_get_products'))return;
    $cleaned=0;
    foreach(wc_get_products(['limit'=>-1,'status'=>['draft','publish','pending','private'],'return'=>'objects']) as $p){$id=$p->get_id();$terms=wp_get_post_terms($id,'product_cat',['fields'=>'all']);$unc=[];$real=[];foreach($terms as $t){if(strtolower($t->slug)==='uncategorized')$unc[]=$t->term_id;else$real[]=$t->term_id;}if($unc&&$real){wp_remove_object_terms($id,$unc,'product_cat');$cleaned++;}}
    update_option('bhc_catalog_taxonomy_hygiene_v150',['cleaned'=>$cleaned,'at'=>current_time('mysql')],false);
},150);
