<?php
if (!defined('ABSPATH')) exit;
add_action('init', function () {
 if (get_option('bhc_catalog_pakshoma_dedupe_fix_v66') || !function_exists('wc_get_product_id_by_sku')) return;
 $target=wc_get_product_id_by_sku('BHC-PAKSHOMA-Q9W');
 if($target){$source=974;$sp=get_post($source);$tp=get_post($target);if($sp&&$tp){wp_update_post(['ID'=>$target,'post_title'=>$sp->post_title,'post_content'=>$sp->post_content,'post_excerpt'=>$sp->post_excerpt,'post_status'=>'draft']);foreach(['_bhc_model','_bhc_brand_latin','_bhc_source_url','_bhc_source_type','_bhc_source_checked_at','_bhc_source_evidence','_bhc_structured_specs','_bhc_highlights','_bhc_faq','_bhc_warranty','_bhc_shipping_note','_bhc_verification_status','_bhc_catalog_quality_state','_bhc_image_prompt','_bhc_seo_title','_bhc_seo_description','_bhc_seo_focus_keyword'] as $key){$v=get_post_meta($source,$key,true);if($v!=='')update_post_meta($target,$key,$v);}update_post_meta($target,'_bhc_image_generation_status','product_specific_generated');update_post_meta($target,'_bhc_image_status','generated_4k_transparent');update_post_meta($target,'_bhc_image_featured_verified','yes');wp_set_object_terms($target,'شست‌وشو','product_cat',true);wp_set_object_terms($target,'پاکشوما','pa_brand',true);}}
 update_option('bhc_catalog_pakshoma_dedupe_fix_v66',['target'=>$target,'at'=>current_time('mysql')],false);
},66);
