<?php
if (!defined('ABSPATH')) require_once dirname(__DIR__) . '/wp-load.php';
if (php_sapi_name() !== 'cli' && !current_user_can('manage_options')) exit("permission denied\n");

$images = [
 'ماشین لباسشویی'=>2402, 'لباسشویی'=>2402, 'یخچال و فریزر'=>2417, 'یخچال'=>2417, 'فریزر'=>2417, 'جاروبرقی'=>2407, 'سرخ‌کن'=>2400, 'سرخکن'=>2400, 'سرخ'=>2400,
 'ماشین ظرفشویی'=>2401, 'اسپرسوساز'=>2410, 'قهوه'=>2410, 'چای‌ساز'=>2410,
 'کتری'=>2410, 'مایکروویو'=>2403, 'سولاردام'=>2403, 'غذاساز'=>2400,
 'خردکن'=>2400, 'مخلوط‌کن'=>2400, 'همزن'=>2400, 'ساندویچ'=>2400, 'تلویزیون'=>2406,
 'رزولوشن'=>2406, 'اتو'=>2404, 'بخار'=>2404, 'کولر'=>2411,
 'تهویه'=>2411, 'تصفیه هوا'=>2396, 'پنکه'=>2411, 'هود'=>2405,
 'انرژی'=>2400, 'گارانتی'=>2400, 'خدمات'=>2400, 'برند'=>2400,
 'مشخصات'=>2400, 'قیمت'=>2400, 'چک‌لیست'=>2400, 'اندازه'=>2400, 'فر توکار'=>2399,
 'تحویل'=>2400, 'فروشنده'=>2400
];
$posts=get_posts(['post_type'=>'post','post_status'=>'publish','posts_per_page'=>-1,'orderby'=>'ID','order'=>'ASC']);
$updated=0; $missing=[];
foreach($posts as $post){
  if($post->post_name==='hello-world' || trim($post->post_title)==='سلام دنیا!') continue;
  $id=0;
  foreach($images as $needle=>$attachment) if(mb_stripos($post->post_title,$needle)!==false){$id=$attachment;break;}
  if(!$id) $id=2400;
  if(!get_post($id)){ $missing[]=$id; continue; }
  set_post_thumbnail($post->ID,$id);
  update_post_meta($id,'_wp_attachment_image_alt',$post->post_title);
  update_post_meta($post->ID,'_bhc_article_image_topic',$id);
  $updated++;
  echo "image={$id} post={$post->ID} title=".mb_substr($post->post_title,0,48)."\n";
}
echo "updated={$updated} missing=".count(array_unique($missing))."\n";
