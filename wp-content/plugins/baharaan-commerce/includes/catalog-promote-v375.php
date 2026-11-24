<?php
if (!defined('ABSPATH')) { exit; }
add_action('init', function () {
 if (!function_exists('wc_get_product') || get_option('bhc_catalog_promote_v375_done')) return;
 $ids=[1482,1480,1474,1472,1470,1468,1456,1454,1226,488,487,486,485,473,465];$promoted=[];
 foreach($ids as $id){$p=wc_get_product($id);if(!$p||get_post_status($id)!=='draft')continue;if(get_post_meta($id,'_bhc_catalog_quality_state',true)!=='reference_verified'||!get_post_meta($id,'_bhc_source_url',true)||!get_post_thumbnail_id($id))continue;$p->set_status('publish');$p->set_catalog_visibility('visible');$p->set_stock_status('outofstock');$p->save();update_post_meta($id,'_bhc_promoted_v375',true);update_post_meta($id,'_bhc_promoted_at','2026-11-20');$promoted[]=$id;}
 update_option('bhc_catalog_promote_v375_done',['promoted'=>$promoted,'at'=>current_time('mysql')],false);
},110);
