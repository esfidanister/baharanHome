<?php
require '/var/www/html/wp-load.php';
foreach ([1942, 1944] as $id) {
    $p = wc_get_product($id);
    echo $id . '|' . $p->get_sku() . '|' . get_post_meta($id, '_bhc_model', true) . '|' . get_post_meta($id, '_bhc_verification_status', true) . '|' . get_post_meta($id, '_bhc_release_blocked', true) . '|' . get_post_meta($id, '_bhc_image_long_edge', true) . '|' . get_post_thumbnail_id($id) . '|' . $p->get_stock_status() . "\n";
}
