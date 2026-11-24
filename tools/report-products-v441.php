<?php
require '/var/www/html/wp-load.php';
$skus=['BHC-EMERSUN-TFH18','BHC-SONY-KD-75X75K'];
foreach($skus as $sku){$id=wc_get_product_id_by_sku($sku);$p=$id?wc_get_product($id):false;echo json_encode(['sku'=>$sku,'id'=>$id,'name'=>$p?$p->get_name():'','status'=>$p?get_post_status($id):'','source'=>$id?get_post_meta($id,'_bhc_source_url',true):'','model'=>$id?get_post_meta($id,'_bhc_model',true):'','brand'=>$id?get_post_meta($id,'_bhc_brand_latin',true):''],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)."\n";}
