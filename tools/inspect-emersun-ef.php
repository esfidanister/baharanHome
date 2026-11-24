<?php
require "/var/www/html/wp-load.php";
foreach (["BHC-EMERSUN-EF10","BHC-EMERSUN-EF11","BHC-EMERSUN-EF12","BHC-EMERSUN-EF13"] as $sku) {
 $id=wc_get_product_id_by_sku($sku); $p=$id?wc_get_product($id):null;
 echo "SKU|$sku|ID|".($id?:0)."|NAME|".($p?$p->get_name():'')."|CAT|".implode(',',wp_get_post_terms($id,'product_cat',['fields'=>'names']))."\n";
 foreach(['_bhc_model','_bhc_source_url','_bhc_source_type','_bhc_catalog_quality_state','_bhc_verification_status','_bhc_structured_specs','_bhc_specs'] as $k) echo $k.'|'.get_post_meta($id,$k,true)."\n";
 echo "DESC|".wp_strip_all_tags($p?$p->get_description():'')."\n";
}
