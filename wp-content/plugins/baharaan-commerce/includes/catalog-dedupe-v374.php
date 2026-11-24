<?php
if (!defined('ABSPATH')) { exit; }
add_action('init', function () {
 if (!function_exists('wc_get_product') || get_option('bhc_catalog_dedupe_v374_done')) return;
 $groups=[1398=>['canonical'=>1554,'sku'=>'BHC-PAKSHOMA-J52-W','reason'=>'همان مدل J52W؛ رکورد 1554 منبع و تصویر اختصاصی معتبرتر دارد'],471=>['canonical'=>1546,'sku'=>'BHC-SONY-K-65S30','reason'=>'همان مدل K-65S30؛ رکورد 1546 به صفحه رسمی Sony.com متصل است'],965=>['canonical'=>183,'sku'=>'SNOWA-SDW-A12W','reason'=>'همان مدل SDW-A12W و همان منبع رسمی؛ رکورد 183 تصویر اختصاصی جدیدتر دارد']];$trashed=[];
 foreach($groups as $id=>$info){if(get_post_status($id)==='publish'&&get_post_status($info['canonical'])==='publish'){wp_update_post(['ID'=>$id,'post_status'=>'trash']);update_post_meta($id,'_bhc_catalog_quality_state','duplicate_candidate_trashed');update_post_meta($id,'_bhc_duplicate_canonical_id',$info['canonical']);update_post_meta($id,'_bhc_duplicate_reason',$info['reason']);$trashed[]=$id;}}
 update_option('bhc_catalog_dedupe_v374_done',['trashed'=>$trashed,'at'=>current_time('mysql')],false);
},100);
