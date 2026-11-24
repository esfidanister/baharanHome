<?php
if (PHP_SAPI !== 'cli') { exit(1); }
require '/var/www/html/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$files = [
  'hero-appliance.webp' => 'تصویر تزئینی هیرو بهاران',
  'categories-appliances.webp' => 'تصاویر تزئینی دسته‌بندی‌ها بهاران',
  'banner-kitchen.webp' => 'پس‌زمینه بنر آشپزخانه بهاران',
  'journal-interior.webp' => 'کاور تزئینی مجله بهاران',
  'reel-interior.webp' => 'کاور تزئینی ریل بهاران',
];
$option = get_option('bhb_generated_media_ids', []);
if (!is_array($option)) $option = [];
$base = '/var/www/html/wp-content/themes/baharaan-home/assets/images/generated/';
$result = [];
foreach ($files as $filename => $title) {
  $source = $base . $filename;
  if (!is_readable($source)) { $result[$filename] = 'missing'; continue; }
  if (!empty($option[$filename]) && get_post((int) $option[$filename])) {
    $result[$filename] = (int) $option[$filename];
    continue;
  }
  $tmp = wp_tempnam($filename);
  if (!$tmp || !copy($source, $tmp)) { $result[$filename] = 'copy-failed'; continue; }
  $attachment_id = media_handle_sideload([
    'name' => $filename,
    'tmp_name' => $tmp,
    'type' => 'image/webp',
    'error' => 0,
    'size' => filesize($source),
  ], 0, ['post_title' => $title, 'post_content' => 'Generated decorative fallback asset. Not a real product or brand logo.']);
  @unlink($tmp);
  if (is_wp_error($attachment_id)) { $result[$filename] = $attachment_id->get_error_message(); continue; }
  $option[$filename] = (int) $attachment_id;
  $result[$filename] = (int) $attachment_id;
}
update_option('bhb_generated_media_ids', $option, false);
echo wp_json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL;
