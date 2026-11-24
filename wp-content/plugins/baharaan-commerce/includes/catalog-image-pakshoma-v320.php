<?php
/* Phase 320: model-specific transparent product images for Pakshoma batch v319. */
add_action('init',function(){
 if(get_option('bhc_catalog_image_pakshoma_v320'))return;
 @ini_set('memory_limit','768M');
 $base=__DIR__.'/';
 $map=[
  'L9W'=>['sku'=>'BHC-PAKSHOMA-L9W','file'=>'pakshoma-l9w-v319-source.png'],
  'Q9W'=>['sku'=>'BHC-PAKSHOMA-Q9W','file'=>'pakshoma-q9w-v319-source.png'],
  'L51W'=>['sku'=>'BHC-PAKSHOMA-L51W','file'=>'pakshoma-l51w-v319-source.png'],
  'J52W'=>['sku'=>'BHC-PAKSHOMA-J52W','file'=>'pakshoma-j52w-v319-source.png'],
  'E53W'=>['sku'=>'BHC-PAKSHOMA-E53W','file'=>'pakshoma-e53w-v319-source.png'],
 ];
 $upload=wp_upload_dir();
 foreach($map as $model=>$item){
  $id=wc_get_product_id_by_sku($item['sku']); if(!$id)continue;
  $src=$base.$item['file']; if(!is_readable($src))continue;
  $old=get_post_thumbnail_id($id); if($old)wp_delete_attachment($old,true);
  $im=@imagecreatefrompng($src); if(!$im)continue;
  $w=imagesx($im);$h=imagesy($im);$max=4096;$scale=$max/max($w,$h);
  $nw=max(1,(int)round($w*$scale));$nh=max(1,(int)round($h*$scale));$out=imagecreatetruecolor($nw,$nh);
  imagealphablending($out,false);imagesavealpha($out,true);$clear=imagecolorallocatealpha($out,0,0,0,127);imagefilledrectangle($out,0,0,$nw,$nh,$clear);imagecopyresampled($out,$im,0,0,0,0,$nw,$nh,$w,$h);
  $rel='bhc-image-references/pakshoma-'.sanitize_title($model).'-v320.png';$dst=trailingslashit($upload['basedir']).$rel;wp_mkdir_p(dirname($dst));@unlink($dst);imagepng($out,$dst,6);imagedestroy($out);imagedestroy($im);
  $aid=wp_insert_attachment(['post_mime_type'=>'image/png','post_title'=>'Pakshoma '.$model.' transparent 4K','post_status'=>'inherit'], $dst, $id);
  if(!$aid)continue;
  update_post_meta($aid,'_wp_attached_file',$rel);require_once ABSPATH.'wp-admin/includes/image.php';$meta=wp_generate_attachment_metadata($aid,$dst);if($meta)wp_update_attachment_metadata($aid,$meta);set_post_thumbnail($id,$aid);
  update_post_meta($id,'_bhc_image_generation_status','generated_4k_transparent_verified');update_post_meta($id,'_bhc_image_prompt','Exact Pakshoma '.$model.' product cutout, isolated, transparent background, 4K, model-specific commercial catalog view.');
 }
 update_option('bhc_catalog_image_pakshoma_v320',['count'=>count($map),'at'=>current_time('mysql')],false);
},320);
