<?php
if (PHP_SAPI !== 'cli') exit(1);
require '/var/www/html/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
$items = [
  24 => 'product-24-TS4130-4k.webp',
  25 => 'product-25-WY1408WTP-4k.webp',
  26 => 'product-26-1412-4k.webp',
  27 => 'product-27-4190-4k.webp',
  28 => 'product-28-UA8500-65-4k.webp',
  29 => 'product-29-8-PRO5-4k.webp',
  30 => 'product-30-AF500-4k.webp',
  31 => 'product-31-EXPRESS-AIRGLIDE-4k.webp',
  32 => 'product-32-NA150-4k.webp',
];
$map = get_option('bhb_generated_product_media', []);
if (!is_array($map)) $map = [];
$base = '/var/www/html/wp-content/uploads/2026/11/';
$result = [];
foreach ($items as $product_id => $filename) {
  $source = $base . $filename;
  if (!is_readable($source)) { $result[$product_id] = 'missing'; continue; }
  if (!empty($map[$product_id]) && get_post((int) $map[$product_id])) {
    set_post_thumbnail($product_id, (int) $map[$product_id]);
    $result[$product_id] = (int) $map[$product_id];
    continue;
  }
  $tmp = wp_tempnam($filename);
  if (!$tmp || !copy($source, $tmp)) { $result[$product_id] = 'copy-failed'; continue; }
  $attachment_id = media_handle_sideload([
    'name' => $filename,
    'tmp_name' => $tmp,
    'type' => 'image/webp',
    'error' => 0,
    'size' => filesize($source),
  ], $product_id, ['post_title' => 'تصویر 4K محصول #' . $product_id, 'post_content' => 'Generated from the verified product reference image.']);
  @unlink($tmp);
  if (is_wp_error($attachment_id)) { $result[$product_id] = $attachment_id->get_error_message(); continue; }
  $map[$product_id] = (int) $attachment_id;
  set_post_thumbnail($product_id, (int) $attachment_id);
  $result[$product_id] = (int) $attachment_id;
}
update_option('bhb_generated_product_media', $map, false);
echo wp_json_encode($result, JSON_UNESCAPED_UNICODE) . PHP_EOL;
