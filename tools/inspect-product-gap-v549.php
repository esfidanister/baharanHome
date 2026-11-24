<?php
require_once '/var/www/html/wp-load.php';
$ids=[482,478,451,344];
foreach($ids as $id){$p=wc_get_product($id);if(!$p)continue;$c=(string)get_post_field('post_content',$id);$fields=['sku'=>$p->get_sku(),'title'=>$p->get_name(),'source'=>get_post_meta($id,'_bhc_source_url',true),'state'=>get_post_meta($id,'_bhc_catalog_quality_state',true),'blocked'=>get_post_meta($id,'_bhc_release_blocked',true),'about'=>mb_stripos($c,'درباره محصول')!==false,'technical'=>mb_stripos($c,'اطلاعات فنی')!==false,'spec'=>get_post_meta($id,'_bhc_structured_specs',true),'seo'=>get_post_meta($id,'_yoast_wpseo_metadesc',true)];echo 'ITEM|'.wp_json_encode($fields,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)."\n";}
