<?php
require_once '/var/www/html/wp-load.php';
$ids=get_posts(['post_type'=>'product','post_status'=>['publish','draft','pending','private'],'posts_per_page'=>-1,'fields'=>'ids']);
$out=['generated_at'=>current_time('mysql'),'total'=>count($ids),'counts'=>[],'products'=>[]];
$counts=['release_ready'=>0,'blocked_source'=>0,'blocked_invalid'=>0,'missing_specs'=>0,'missing_content'=>0,'missing_image'=>0,'missing_seo'=>0,'missing_taxonomy'=>0,'duplicate_sku'=>0];
$skuMap=[];foreach($ids as $id){$p=wc_get_product($id);if($p)$skuMap[$p->get_sku()][]=$id;}
foreach($ids as $id){$p=wc_get_product($id);if(!$p)continue;$sku=(string)$p->get_sku();$brand=wp_get_post_terms($id,'pa_brand',['fields'=>'names']);$cat=wp_get_post_terms($id,'product_cat',['fields'=>'names']);$state=(string)get_post_meta($id,'_bhc_catalog_quality_state',true);$source=(string)get_post_meta($id,'_bhc_source_url',true);$spec=(string)get_post_meta($id,'_bhc_structured_specs',true);$desc=wp_strip_all_tags((string)$p->get_description());$img=(string)get_post_meta($id,'_bhc_image_generation_status',true);$seoTitle=(string)get_post_meta($id,'_bhc_seo_title',true);$seoDesc=(string)get_post_meta($id,'_bhc_seo_description',true);$reasons=[];
 if($state==='reference_invalid'){$reasons[]='invalid_reference';$counts['blocked_invalid']++;}elseif($state!=='reference_verified'||$source===''){$reasons[]='exact_source_required';$counts['blocked_source']++;}
 if($spec===''||json_decode($spec,true)===null){$reasons[]='structured_specs_missing';$counts['missing_specs']++;}
 foreach(['درباره محصول','اطلاعات فنی','پرسش‌های متداول'] as $h)if(mb_strpos($desc,$h)===false){$reasons[]='content_'.$h;$counts['missing_content']++;break;}
 if(!$p->get_image_id()||!in_array($img,['generated_4k_transparent_verified','product_specific_generated','verified'],true)){$reasons[]='product_image_required';$counts['missing_image']++;}
 if($seoTitle===''||$seoDesc===''){$reasons[]='seo_meta_missing';$counts['missing_seo']++;}
 if(empty($brand)||empty($cat)){$reasons[]='taxonomy_missing';$counts['missing_taxonomy']++;}
 if($sku===''||count($skuMap[$sku]??[])>1){$reasons[]='duplicate_or_empty_sku';$counts['duplicate_sku']++;}
 if(!$reasons)$counts['release_ready']++;$out['products'][]=['id'=>$id,'sku'=>$sku,'name'=>$p->get_name(),'brand'=>$brand[0]??'','category'=>$cat[0]??'','state'=>$state,'reasons'=>$reasons];
}
$out['counts']=$counts;$dir=wp_upload_dir()['basedir'].'/catalog-audits';if(!is_dir($dir))wp_mkdir_p($dir);file_put_contents($dir.'/release-readiness-v577.json',wp_json_encode($out,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT));
foreach($counts as $k=>$v)echo strtoupper($k).'|'.$v.PHP_EOL;echo 'REPORT|'.$dir.'/release-readiness-v577.json'.PHP_EOL;
