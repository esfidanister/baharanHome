<?php
require_once '/var/www/html/wp-load.php';

$ids = get_posts([
    'post_type' => 'product',
    'post_status' => ['publish','draft','pending','private'],
    'posts_per_page' => -1,
    'fields' => 'ids',
]);
$brandCounts = [];
$remainingByBrand = [];
$statusCounts = [];
$issueTypes = [];
$duplicates = [];
$imageChecks = ['with_thumbnail'=>0, 'generated_verified'=>0, 'missing'=>0];
$sourceChecks = ['source_url'=>0, 'reference_verified'=>0, 'ready_for_release'=>0];

foreach ($ids as $id) {
    $p = wc_get_product($id);
    if (!$p) continue;
    $sku = $p->get_sku();
    $terms = wp_get_post_terms($id, 'pa_brand', ['fields'=>'names']);
    $brand = $terms[0] ?? 'بدون برند';
    $brandCounts[$brand] = ($brandCounts[$brand] ?? 0) + 1;
    $status = get_post_status($id);
    $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;
    $source = (bool) get_post_meta($id, '_bhc_source_url', true);
    $state = get_post_meta($id, '_bhc_catalog_quality_state', true);
    $ready = get_post_meta($id, '_bhc_release_status', true) === 'ready_for_release'
        || get_post_meta($id, '_bhc_catalog_quality_state', true) === 'ready_for_release';
    $thumb = (bool) get_post_thumbnail_id($id);
    $imageState = get_post_meta($id, '_bhc_image_generation_status', true);
    $sourceChecks['source_url'] += $source ? 1 : 0;
    $sourceChecks['reference_verified'] += $state === 'reference_verified' ? 1 : 0;
    $sourceChecks['ready_for_release'] += $ready ? 1 : 0;
    $imageChecks['with_thumbnail'] += $thumb ? 1 : 0;
    $imageChecks['generated_verified'] += $imageState === 'generated_4k_transparent_verified' ? 1 : 0;
    $imageChecks['missing'] += $thumb ? 0 : 1;
    $needs = [];
    if (!$source) $needs[] = 'no-source';
    if (!$thumb) $needs[] = 'no-image';
    if ($state !== 'reference_verified') $needs[] = 'not-reference-verified';
    if ($needs) {
        $remainingByBrand[$brand] = ($remainingByBrand[$brand] ?? 0) + 1;
        foreach ($needs as $need) $issueTypes[$need] = ($issueTypes[$need] ?? 0) + 1;
    }
    $key = strtoupper(preg_replace('/[^A-Z0-9]+/', '', str_replace('BHC-', '', $sku)));
    if ($key) $duplicates[$key][] = $id;
}

echo 'TOTAL|'.count($ids)."\n";
echo "BRANDS\n"; ksort($brandCounts); foreach ($brandCounts as $b=>$n) echo $b.'|'.$n."\n";
echo "STATUSES\n"; ksort($statusCounts); foreach ($statusCounts as $s=>$n) echo $s.'|'.$n."\n";
echo "SOURCES\n"; foreach ($sourceChecks as $k=>$n) echo $k.'|'.$n."\n";
echo "IMAGES\n"; foreach ($imageChecks as $k=>$n) echo $k.'|'.$n."\n";
echo "REMAINING_BY_BRAND\n"; ksort($remainingByBrand); foreach ($remainingByBrand as $b=>$n) echo $b.'|'.$n."\n";
echo "ISSUE_TYPES\n"; ksort($issueTypes); foreach ($issueTypes as $k=>$n) echo $k.'|'.$n."\n";
echo "DUPLICATES\n"; foreach ($duplicates as $k=>$list) if (count($list)>1) echo $k.'|'.implode(',', $list)."\n";
