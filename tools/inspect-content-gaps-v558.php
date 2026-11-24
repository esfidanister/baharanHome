<?php
require_once '/var/www/html/wp-load.php';
foreach([370,376,354,27] as $id){$p=wc_get_product($id);echo "ID|$id|".$p->get_name()."|state=".get_post_meta($id,'_bhc_catalog_quality_state',true)."|source=".get_post_meta($id,'_bhc_source_url',true)."\n";echo wp_strip_all_tags((string)get_post_field('post_content',$id))."\n---\n";}
