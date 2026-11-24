<?php
require_once '/var/www/html/wp-load.php';
$ids=get_posts(['post_type'=>'product','post_status'=>['publish','draft','pending','private'],'posts_per_page'=>-1,'fields'=>'ids','orderby'=>'ID','order'=>'ASC']);
$n=0;
foreach($ids as $id){$p=wc_get_product($id);if(!$p)continue;$u=get_post_meta($id,'_bhc_source_url',true);$s=get_post_meta($id,'_bhc_catalog_quality_state',true);if($u&&$s==='reference_verified')continue;$terms=wp_get_post_terms($id,'pa_brand',['fields'=>'names']);$cats=wp_get_post_terms($id,'product_cat',['fields'=>'names']);$attrs=[];foreach($p->get_attributes() as $a){$v=$a->is_taxonomy()?wc_get_product_terms($id,$a->get_name(),['fields'=>'names']):$a->get_options();if($v)$attrs[wc_attribute_label($a->get_name())]=implode('، ',$v);}echo 'GAP|'.$id.'|'.$p->get_sku().'|'.$p->get_name().'|'.($terms[0]??'').'|'.($cats[0]??'').'|'.wp_json_encode($attrs,JSON_UNESCAPED_UNICODE)."\n";$n++;}
echo 'COUNT|'.$n."\n";
