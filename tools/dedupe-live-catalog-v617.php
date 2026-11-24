<?php
require_once '/var/www/html/wp-load.php';

// Canonical product IDs were selected from the duplicate audit by completeness,
// source quality, and the current public URL. Duplicates are trashed (recoverable)
// and never hard-deleted.
$map = [
  1548 => 477, 1547 => 476, 1545 => 474,
  1523 => 1480,
  1397 => 145,
  1381 => 976, 146 => 976,
  1373 => 138,
  488 => 1032,
  486 => 1028,
  139 => 973,
];
$moved = 0;
$rows = [];
foreach ($map as $duplicate => $canonical) {
  $dp = wc_get_product($duplicate);
  $cp = wc_get_product($canonical);
  if (!$dp || !$cp) { echo "SKIP|{$duplicate}|{$canonical}\n"; continue; }
  if (get_post_status($duplicate) === 'trash') { echo "ALREADY_TRASH|{$duplicate}|{$canonical}\n"; continue; }
  $oldSlug = (string)get_post_field('post_name', $duplicate);
  $newUrl = (string)get_permalink($canonical);
  update_post_meta($duplicate, '_bhc_duplicate_of', (string)$canonical);
  update_post_meta($duplicate, '_bhc_duplicate_old_slug', $oldSlug);
  update_post_meta($duplicate, '_bhc_duplicate_redirect_to', $newUrl);
  update_post_meta($duplicate, '_bhc_duplicate_merged_at', current_time('mysql'));
  wp_trash_post($duplicate);
  $moved++;
  $rows[] = [$duplicate, $canonical, $oldSlug, $newUrl];
  echo "TRASHED|{$duplicate}|CANONICAL|{$canonical}|SLUG|{$oldSlug}\n";
}
echo 'TRASHED_TOTAL|'.$moved."\n";
echo 'RECOVERABLE|yes\n';
