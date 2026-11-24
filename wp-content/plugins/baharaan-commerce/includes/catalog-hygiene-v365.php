<?php
if (!defined('ABSPATH')) { exit; }
add_action('init', function () {
    if (get_option('bhc_catalog_hygiene_v365_done')) return;
    $ids = [1163, 974, 469, 470, 472];
    $moved = [];
    foreach ($ids as $id) {
        if (get_post_status($id) === 'draft') {
            wp_update_post(['ID'=>$id,'post_status'=>'trash']);
            update_post_meta($id,'_bhc_catalog_quality_state','duplicate_candidate_trashed');
            $moved[]=$id;
        }
    }
    update_option('bhc_catalog_hygiene_v365_done',['trashed'=>$moved,'at'=>current_time('mysql')],false);
});
