<?php
if (!defined('ABSPATH')) { exit; }
add_action('init', function () {
 if (get_option('bhc_catalog_empty_quarantine_v376_done')) return;
 $ids=[131,132];$moved=[];foreach($ids as $id){if(get_post_status($id)==='draft'&&!get_the_title($id)&&!get_post_meta($id,'_sku',true)){wp_update_post(['ID'=>$id,'post_status'=>'trash']);update_post_meta($id,'_bhc_catalog_quality_state','malformed_empty_record_trashed');update_post_meta($id,'_bhc_quarantine_reason','رکورد قدیمی بدون عنوان، SKU، منبع و مشخصات قابل استفاده');$moved[]=$id;}}update_option('bhc_catalog_empty_quarantine_v376_done',['trashed'=>$moved,'at'=>current_time('mysql')],false);
},120);
