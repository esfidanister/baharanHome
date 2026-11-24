<?php
require_once '/var/www/html/wp-load.php';
$id = wc_get_product_id_by_sku('BHC-GPLUS-GTV-50LU722S');
if (!$id) { echo "MISSING\n"; exit; }
$content = (string)get_post_field('post_content', $id);
if (mb_stripos($content, 'وضعیت کاتالوگ') === false) {
  $content .= '<h3>وضعیت کاتالوگ</h3><p>مدل GTV-50LU722S با دو منبع مدل‌محور مستقل بررسی و تأیید شده است. کالا فعلاً ناموجود است و قیمت نمایش داده نمی‌شود.</p>';
  wp_update_post(['ID'=>$id,'post_content'=>$content]);
}
echo "FIXED|$id|".(mb_stripos((string)get_post_field('post_content',$id),'وضعیت کاتالوگ')!==false?'1':'0')."\n";
