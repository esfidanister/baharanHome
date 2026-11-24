<?php
$p=wc_get_products(['limit'=>-1]);
$m=['total'=>count($p),'verified'=>0,'source'=>0,'content'=>0,'images'=>0,'img4k'=>0,'ready'=>0];
foreach($p as $x){if($x->get_meta('_bhc_verification_status')==='verified_reference')$m['verified']++;if($x->get_meta('_bhc_source_url'))$m['source']++;if($x->get_meta('_bhc_structured_specs'))$m['content']++;if($x->get_image_id())$m['images']++;if((int)$x->get_meta('_bhc_image_long_edge')>=4096)$m['img4k']++;if($x->get_meta('_bhc_catalog_quality_state')==='ready')$m['ready']++;}
echo json_encode($m,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT),PHP_EOL;
