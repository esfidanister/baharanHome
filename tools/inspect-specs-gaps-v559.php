<?php
require_once '/var/www/html/wp-load.php';
foreach([370,376,354,27] as $id){echo "ID|$id|".wp_json_encode(get_post_meta($id,'_bhc_specs',true),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)."\n";}
