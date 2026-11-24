<?php
if (!defined('ABSPATH')) exit;
add_action('init', function () {
 if (get_option('bhc_image_reference_batch_v53') || !function_exists('wc_get_product_id_by_sku')) return;
 $rows = [
  ['sku'=>'LG-F2Y1VYP6J','url'=>'https://www.lg.com/ae/images/washing-machines/md07601062/gallery/D-01.jpg','evidence'=>'تصویر رسمی Front view از صفحه رسمی مدل LG UAE بررسی و با مدل F2Y1VYP6J تطبیق شد.'],
  ['sku'=>'LG-OLED55B46LA','url'=>'https://www.lg.com/ae/images/tvs-soundbars/md07597298/gallery/oled55b46laDesktop-1100x730.jpg','evidence'=>'تصویر رسمی صفحه محصول LG OLED evo B4 مدل OLED55B46LA در LG UAE بررسی و با مدل تطبیق شد.'],
 ];
 $updated = 0;
 foreach ($rows as $row) { $id=wc_get_product_id_by_sku($row['sku']); if (!$id) continue; update_post_meta($id,'_bhc_image_reference_url',$row['url']); update_post_meta($id,'_bhc_image_reference_evidence',$row['evidence']); update_post_meta($id,'_bhc_image_reference_checked_at','2026-11-19'); update_post_meta($id,'_bhc_image_generation_status','product_specific_generated'); $updated++; }
 update_option('bhc_image_reference_batch_v53',['updated'=>$updated,'at'=>current_time('mysql')],false);
}, 53);
