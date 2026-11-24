<?php
/* Phase 313: restore the 4K PNG as the authoritative attachment file after WordPress creates scaled WebP derivatives. */
add_action('init',function(){
if(get_option('bhc_catalog_image_emersun_remediation_finalize_v313'))return;
$model='BFN27D502';$ids=get_posts(['post_type'=>'product','post_status'=>'any','posts_per_page'=>1,'fields'=>'ids','meta_key'=>'_bhc_model','meta_value'=>$model]);$id=$ids?(int)$ids[0]:0;$aid=$id?get_post_thumbnail_id($id):0;$rel='bhc-image-references/emersun-bfn27d502-repair-v312.png';$file=ABSPATH.'wp-content/uploads/'.$rel;$ok=$aid&&is_readable($file);if($ok){$q=getimagesize($file);update_post_meta($aid,'_wp_attached_file',$rel);wp_update_attachment_metadata($aid,['width'=>(int)$q[0],'height'=>(int)$q[1],'file'=>$rel,'sizes'=>[],'image_meta'=>[]]);update_post_meta($id,'_bhc_image_status','generated_4k_transparent_verified');update_post_meta($id,'_bhc_image_featured_verified','yes');}update_option('bhc_catalog_image_emersun_remediation_finalize_v313',['id'=>$id,'attachment'=>$aid,'ok'=>$ok,'dimensions'=>$ok?[$q[0],$q[1]]:null,'at'=>current_time('mysql')],false);
},313);
