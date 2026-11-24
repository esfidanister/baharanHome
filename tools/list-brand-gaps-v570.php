<?php
require_once '/var/www/html/wp-load.php';
global $wpdb;
$rows=$wpdb->get_results("SELECT t.name, COUNT(*) c FROM {$wpdb->term_taxonomy} tt JOIN {$wpdb->terms} t ON t.term_id=tt.term_id JOIN {$wpdb->term_relationships} tr ON tr.term_taxonomy_id=tt.term_taxonomy_id JOIN {$wpdb->postmeta} pm ON pm.post_id=tr.object_id AND pm.meta_key='_bhc_catalog_quality_state' AND pm.meta_value='reference_needs_verification' WHERE tt.taxonomy='pa_brand' GROUP BY t.name ORDER BY c DESC");
foreach($rows as $r) echo $r->name.'|'.$r->c.PHP_EOL;
