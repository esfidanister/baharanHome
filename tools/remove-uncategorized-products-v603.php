<?php
require_once '/var/www/html/wp-load.php';
$unc = get_term_by('slug','uncategorized','product_cat');
if (!$unc) { echo "UNCATEGORIZED_MISSING\n"; exit; }
$ids = get_posts(['post_type'=>'product','post_status'=>'any','posts_per_page'=>-1,'fields'=>'ids','tax_query'=>[['taxonomy'=>'product_cat','field'=>'term_id','terms'=>[(int)$unc->term_id]]]]);
$only = [];
foreach ($ids as $id) {
  $terms = wp_get_post_terms($id,'product_cat',['fields'=>'ids']);
  $others = array_values(array_diff(array_map('intval',$terms),[(int)$unc->term_id]));
  if (!$others) $only[] = (int)$id;
}
if ($only) { echo 'STOP_ONLY_UNCATEGORIZED|'.implode(',',$only).'\n'; exit; }
$removed=0;
foreach ($ids as $id) { $r=wp_remove_object_terms($id,(int)$unc->term_id,'product_cat'); if ($r && !is_wp_error($r)) $removed++; }
// Choose an existing real root category as WooCommerce fallback, then delete the placeholder term.
$fallback = get_term(17,'product_cat');
if (!$fallback || is_wp_error($fallback)) { echo "STOP_NO_FALLBACK\n"; exit; }
update_option('default_product_cat',(int)$fallback->term_id);
$deleted = wp_delete_term((int)$unc->term_id,'product_cat');
echo 'REMOVED|'.$removed.'|FALLBACK|'.$fallback->term_id.'|'.$fallback->name.'|DELETED|'.($deleted?'1':'0').'\n';
