<?php
require '/var/www/html/wp-load.php';
$ids=[518,534,504,511,497];foreach($ids as $id){$p=wc_get_product($id);echo json_encode(['id'=>$id,'sku'=>$p?$p->get_sku():'','name'=>$p?$p->get_name():'','cat'=>$p?wp_get_post_terms($id,'product_cat',['fields'=>'names']):[],'desc'=>$p?wp_strip_all_tags($p->get_description()):''],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)."\n";}
