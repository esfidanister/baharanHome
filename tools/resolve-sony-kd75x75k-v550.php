<?php
require_once '/var/www/html/wp-load.php';
$id=482;
$p=wc_get_product($id);
if(!$p){echo "MISSING|$id\n";exit;}
$title='تلویزیون سونی مدل KD-75X75K سایز ۷۵ اینچ';
$source='https://khodienmaygiare.com.vn/smart-tivi-sony-4k-75-inch-kd-75x75k';
$official='https://www.sony.com/lr/electronics/televisions/x75k-series/specifications';
$specs=[
 'برند'=>'Sony',
 'مدل دقیق'=>'KD-75X75K',
 'سری'=>'BRAVIA X75K',
 'سال مدل'=>'2022',
 'اندازه صفحه'=>'۷۵ اینچ',
 'وضوح تصویر'=>'4K Ultra HD؛ 3840×2160 پیکسل',
 'فناوری تصویر'=>'HDR، پردازنده 4K Processor X1، 4K X-Reality PRO و Motionflow XR',
 'سیستم‌عامل'=>'Google TV',
 'درگاه HDMI'=>'۳ ورودی HDMI',
 'درگاه USB'=>'۲ ورودی USB',
 'شبکه بی‌سیم'=>'Wi‑Fi 802.11a/b/g/n/ac',
 'بلوتوث'=>'Bluetooth 5.0',
 'قابلیت‌های هوشمند'=>'Chromecast built-in، Apple AirPlay 2 و HomeKit',
 'وضعیت'=>'ناموجود'
];
$lines='';foreach($specs as $k=>$v)$lines.='<li><strong>'.esc_html($k).':</strong> '.esc_html($v).'</li>';
$desc='<p>'.esc_html($title).' یک تلویزیون هوشمند از سری BRAVIA X75K سونی است. این رکورد با صفحه‌ی فروشگاهیِ دقیق مدل و صفحه‌ی رسمی مشخصات خانواده‌ی X75K تطبیق داده شده است. قیمت درج نشده و وضعیت فعلی کالا ناموجود است.</p>';
$desc.='<h3>درباره محصول</h3><p>مدل KD-75X75K برای تماشای محتوای 4K، فیلم و استفاده از امکانات هوشمند Google TV طراحی شده است. پردازنده‌ی 4K Processor X1، فناوری 4K X-Reality PRO و پشتیبانی HDR برای پردازش و ارتقای تصویر به‌کار می‌روند. مشخصات منطقه‌ای، زبان منو، گارانتی و اقلام همراه باید پیش از تأمین با پلاک دستگاه و نمایندگی مجاز کنترل شوند.</p>';
$desc.='<h3>اطلاعات فنی</h3><ul>'.$lines.'</ul>';
$desc.='<h3>وضعیت تأمین و اصالت</h3><p>این محصول فعلاً ناموجود است. پیش از عرضه باید شماره مدل، کشور مقصد، شماره سریال، سلامت پنل، لوازم همراه و شرایط گارانتی ایران کنترل شود.</p>';
$desc.='<h3>منابع بررسی</h3><p><a href="'.esc_url($source).'" target="_blank" rel="nofollow noopener">صفحه فروشگاهی مدل دقیق KD-75X75K</a> و <a href="'.esc_url($official).'" target="_blank" rel="nofollow noopener">صفحه رسمی مشخصات سری Sony X75K</a>.</p>';
$p->set_name($title);$p->set_description($desc);$p->set_short_description($title.' · تلویزیون 4K هوشمند Google TV · ناموجود · منبع تطبیق‌شده');$p->set_stock_status('outofstock');$p->save();
update_post_meta($id,'_bhc_model','KD-75X75K');update_post_meta($id,'_bhc_brand_latin','Sony');update_post_meta($id,'_bhc_source_url',$source);update_post_meta($id,'_bhc_source_urls',[$source,$official]);update_post_meta($id,'_bhc_source_type','cross_checked_exact_retailer_and_official_series');update_post_meta($id,'_bhc_source_checked_at','2026-11-21');update_post_meta($id,'_bhc_source_evidence',$source.' | صفحه فروشگاهی دقیق مدل KD-75X75K برای هویت مدل بررسی شد. '.$official.' | وضوح 4K، HDR، Google TV، اتصالات و قابلیت‌های سری رسمی تطبیق داده شد.');update_post_meta($id,'_bhc_structured_specs',wp_json_encode($specs,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));update_post_meta($id,'_bhc_specs',$specs);update_post_meta($id,'_bhc_catalog_quality_state','reference_verified');update_post_meta($id,'_bhc_verification_status','verified');update_post_meta($id,'_bhc_source_audit_status','exact_model_verified');update_post_meta($id,'_bhc_release_blocked','no');update_post_meta($id,'_bhc_release_block_reason','');update_post_meta($id,'_bhc_release_blocked_reason','');update_post_meta($id,'_bhc_no_price_message','قیمت نمایش داده نمی‌شود. وضعیت کالا: ناموجود.');update_post_meta($id,'_yoast_wpseo_metadesc','تلویزیون سونی KD-75X75K سایز ۷۵ اینچ با وضوح 4K، HDR، Google TV و پردازنده 4K X1؛ مشخصات مستند و وضعیت ناموجود.');
echo 'RESOLVED|'.$id.'|'.$p->get_sku()."\n";
