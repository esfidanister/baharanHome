<?php
require_once '/var/www/html/wp-load.php';
$q=new WP_Query(['post_type'=>'product','post_status'=>['publish','draft','pending','private'],'posts_per_page'=>-1,'fields'=>'ids']);
$brands=[];$issues=[];$dupes=[];
foreach($q->posts as $id){$sku=wc_get_product($id)?wc_get_product($id)->get_sku():'';$brand=wp_get_post_terms($id,'pa_brand',['fields'=>'names']);$brand=$brand[0]??'بدون برند';$brands[$brand]=($brands[$brand]??0)+1;$state=get_post_meta($id,'_bhc_catalog_quality_state',true);$source=get_post_meta($id,'_bhc_source_url',true);$img=get_post_thumbnail_id($id);if(!$source||!$img||$state!=='reference_verified')$issues[]=$id.'|'.get_post_status($id).'|'.$sku.'|'.($source?'source':'no-source').'|'.($img?'image':'no-image').'|'.($state?:'no-state');$key=strtoupper(preg_replace('/[^A-Z0-9]+/','',str_replace('BHC-','',$sku)));if($key){$dupes[$key][]=$id;}}
echo "COUNTS\n";foreach($brands as $b=>$n)echo $b.'|'.$n."\n";echo 'TOTAL|'.count($q->posts)."\n";echo 'ISSUES|'.count($issues)."\n";foreach(array_slice($issues,0,120) as $x)echo $x."\n";echo "DUPLICATES\n";foreach($dupes as $k=>$ids)if(count($ids)>1)echo $k.'|'.implode(',',$ids)."\n";
