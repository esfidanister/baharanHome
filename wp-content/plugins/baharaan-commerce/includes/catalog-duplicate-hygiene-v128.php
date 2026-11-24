<?php
if (!defined('ABSPATH')) exit;
add_action('init',function(){
    if(get_option('bhc_catalog_duplicate_hygiene_v128'))return;
    $pairs=[[1163,1162]];$updated=0;
    foreach($pairs as $pair){$duplicate=(int)$pair[0];$canonical=(int)$pair[1];$source=get_post($canonical);$target=get_post($duplicate);if(!$source||!$target)continue;
        wp_update_post(['ID'=>$duplicate,'post_title'=>$source->post_title,'post_content'=>$source->post_content,'post_excerpt'=>$source->post_excerpt,'post_status'=>'draft']);
        foreach(['_bhc_model','_bhc_brand_latin','_bhc_source_url','_bhc_source_type','_bhc_source_checked_at','_bhc_source_evidence','_bhc_structured_specs','_bhc_highlights','_bhc_faq','_bhc_warranty','_bhc_shipping_note','_bhc_verification_status','_bhc_image_prompt','_bhc_seo_title','_bhc_seo_description','_bhc_seo_focus_keyword'] as $key){$v=get_post_meta($canonical,$key,true);if($v!=='')update_post_meta($duplicate,$key,$v);}
        update_post_meta($duplicate,'_bhc_duplicate_of',$canonical);update_post_meta($duplicate,'_bhc_duplicate_reason','همان SKU و همان مدل دقیق؛ رکورد اصلی برای انتشار و ایندکس انتخاب شده است.');update_post_meta($duplicate,'_bhc_catalog_quality_state','duplicate_candidate');update_post_meta($duplicate,'_bhc_image_generation_status','duplicate_not_for_release');update_post_meta($duplicate,'_bhc_noindex','yes');wp_set_object_terms($duplicate,wp_get_object_terms($canonical,'product_cat',['fields'=>'ids']),'product_cat',false);wp_set_object_terms($duplicate,wp_get_object_terms($canonical,'pa_brand',['fields'=>'ids']),'pa_brand',false);$updated++;
    }
    update_option('bhc_catalog_duplicate_hygiene_v128',['updated'=>$updated,'pairs'=>count($pairs),'at'=>current_time('mysql')],false);
},128);
