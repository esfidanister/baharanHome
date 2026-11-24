<?php
require '/var/www/html/wp-load.php';
$skus = ['UA8500-65','TS4130','NA150','EXPRESS-AIRGLIDE','8-PRO5','1412','AF500'];
$done = 0;
foreach ($skus as $sku) {
    $id = wc_get_product_id_by_sku($sku);
    if (!$id) { echo "MISSING|$sku\n"; continue; }
    $p = wc_get_product($id);
    if ($p) { $p->set_stock_status('outofstock'); $p->save(); $done++; echo "OUTOFSTOCK|$sku|$id\n"; }
}
echo "NORMALIZED|$done\n";
