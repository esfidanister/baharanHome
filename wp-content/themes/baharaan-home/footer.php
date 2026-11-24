<?php
if (!defined('ABSPATH')) exit;
$opts=get_option('bhc_options',[]);
$shop=bhb_shop_url();
$cart=function_exists('wc_get_cart_url')?wc_get_cart_url():$shop;
$account=function_exists('wc_get_page_permalink')?wc_get_page_permalink('myaccount'):wp_login_url();
$checkout=function_exists('wc_get_checkout_url')?wc_get_checkout_url():$cart;
$columns=[
 'دسترسی سریع'=>array_filter([
  'فروشگاه'=>$shop,
  'سبد خرید'=>$cart,
  'پرداخت'=>$checkout,
  'حساب کاربری'=>$account,
 ]),
 'خدمات مشتریان'=>array_filter([
  'پیگیری سفارش'=>bhb_page_url('order-tracking'),
  'پشتیبانی'=>bhb_page_url('contact'),
  'روش‌های ارسال'=>bhb_page_url('shipping'),
  'شرایط بازگشت'=>bhb_page_url('returns'),
 ]),
 'درباره بهاران'=>array_filter([
  'حریم خصوصی'=>bhb_page_url('privacy-policy'),
  'تماس با فروشگاه'=>bhb_page_url('contact'),
 ]),
];
?>
</main>
<footer class="bh-footer" aria-label="پاورقی سایت">
 <div class="bh-footer-inner">
  <div class="bh-footer-brand">
   <a class="bh-brand" href="<?php echo esc_url(home_url('/')); ?>"><span class="bh-mark" aria-hidden="true"><svg viewBox="0 0 48 48" width="26" height="26"><path d="M8 22 24 8l16 14v16a2 2 0 0 1-2 2H10a2 2 0 0 1-2-2V22z" fill="none" stroke="#fff" stroke-width="2.4" stroke-linejoin="round"/><path d="M20 40V26h8v14" fill="none" stroke="#fff" stroke-width="2.4" stroke-linejoin="round"/></svg></span><span class="bh-brand-name"><?php echo esc_html($opts['brand_first']??'بهاران'); ?> <em><?php echo esc_html($opts['brand_second']??'هوم'); ?></em></span></a>
   <p><?php echo esc_html(bhb_copy('footer_about',$opts['footer_intro']??'انتخاب لوازم خانگی با مشخصات، قیمت و وضعیت موجودی به‌روز.')); ?></p>
   <?php
   $phone=trim((string)($opts['phone']??'')); if($phone==='') $phone=bhb_copy('footer_phone','');
   $phone2=trim((string)($opts['phone_2']??''));
   $hours=bhb_copy('footer_hours',$opts['support_hours']??'');
   $address=trim((string)($opts['store_address']??'')); if($address==='') $address=bhb_copy('footer_address','');
   $store_name=trim((string)($opts['store_name']??''));
   $social_base=get_theme_file_uri('assets/img/social/');
   $wa=function($raw){$d=preg_replace('/\D/','',(string)$raw); if($d!==''&&$d[0]==='0') $d='98'.substr($d,1); return $d;};
   $phones=array_values(array_filter([$phone,$phone2]));
   $channels=array_filter([
    ['label'=>'اینستاگرام','hint'=>'baharaan.home','href'=>$opts['instagram']??'','icon'=>'instagram.webp','tone'=>'ig'],
    ['label'=>'تلگرام','hint'=>'کانال فروشگاه','href'=>$opts['telegram']??'','icon'=>'telegram.webp','tone'=>'tg'],
    ['label'=>'ادمین','hint'=>'پاسخ در تلگرام','href'=>$opts['telegram_admin']??'','icon'=>'telegram.webp','tone'=>'admin'],
    ['label'=>'بله','hint'=>'baharaan_home','href'=>$opts['bale']??'','icon'=>'bale.webp','tone'=>'bale'],
    ['label'=>'ایتا','hint'=>'پیام در ایتا','href'=>$opts['eitaa']??'','icon'=>'eitaa.webp','tone'=>'eitaa'],
   ],fn($row)=>trim((string)$row['href'])!=='');
   ?>
   <div class="bh-footer-contact">
   <?php if($store_name!==''): ?><p class="bh-footer-store"><?php echo esc_html($store_name); ?></p><?php endif; ?>
   <?php if($address!==''): ?><p class="bh-footer-address"><img src="<?php echo esc_url($social_base.'pin.webp'); ?>" alt="" width="18" height="18"><span><?php echo nl2br(esc_html($address)); ?></span></p><?php endif; ?>
   <?php if($phones): ?><div class="bh-footer-phones"><?php foreach($phones as $i=>$num): $dial=preg_replace('/\s+/','',$num); $wad=$wa($num); ?>
    <div class="bh-phone-card">
     <a class="bh-phone-call" href="tel:<?php echo esc_attr($dial); ?>"><b><?php echo esc_html($num); ?></b><small><?php echo $i===0?'موبایل فروشگاه':'موبایل دوم'; ?></small></a>
     <?php if($wad!==''): ?><a class="bh-phone-wa" href="<?php echo esc_url('https://wa.me/'.$wad); ?>" target="_blank" rel="noopener noreferrer"><img src="<?php echo esc_url($social_base.'whatsapp.webp'); ?>" alt="" width="22" height="22"><span>واتساپ</span></a><?php endif; ?>
    </div>
   <?php endforeach; ?></div><?php endif; ?>
   <?php if(!empty($opts['email'])): ?><a class="bh-footer-mail" href="mailto:<?php echo esc_attr(sanitize_email($opts['email'])); ?>"><?php echo esc_html($opts['email']); ?></a><?php endif; ?>
   <?php if($hours!==''): ?><p class="bh-footer-hours">ساعت پاسخ‌گویی: <?php echo esc_html($hours); ?></p><?php endif; ?>
   <?php if($channels): ?><div class="bh-footer-social" aria-label="شبکه‌های اجتماعی"><?php foreach($channels as $social): ?><a class="tone-<?php echo esc_attr($social['tone']); ?>" href="<?php echo esc_url($social['href']); ?>" target="_blank" rel="noopener noreferrer"><img src="<?php echo esc_url($social_base.$social['icon']); ?>" alt="" width="28" height="28"><span><b><?php echo esc_html($social['label']); ?></b><small><?php echo esc_html($social['hint']); ?></small></span></a><?php endforeach; ?></div><?php endif; ?>
   </div>
   <?php $seals=[]; if(!empty($opts['enamad_image'])&&!empty($opts['enamad_link'])) $seals[]=['label'=>'نماد اعتماد الکترونیکی','href'=>$opts['enamad_link'],'img'=>$opts['enamad_image']]; if(!empty($opts['samandehi_image'])&&!empty($opts['samandehi_link'])) $seals[]=['label'=>'مجوز ساماندهی','href'=>$opts['samandehi_link'],'img'=>$opts['samandehi_image']]; if($seals): ?>
   <div class="bh-footer-seals"><?php foreach($seals as $seal): ?><a href="<?php echo esc_url($seal['href']); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo esc_attr($seal['label']); ?>"><img src="<?php echo esc_url($seal['img']); ?>" alt="<?php echo esc_attr($seal['label']); ?>" width="72" height="80"></a><?php endforeach; ?></div>
   <?php endif; ?>
  </div>
  <div class="bh-footer-cols">
  <?php foreach($columns as $title=>$links): if(!$links) continue; ?>
  <nav aria-label="<?php echo esc_attr($title); ?>"><h2><?php echo esc_html($title); ?></h2><?php foreach($links as $label=>$url): ?><a href="<?php echo esc_url($url); ?>"><?php echo esc_html($label); ?></a><?php endforeach; ?></nav>
  <?php endforeach; ?>
  </div>
 </div>
 <ul class="bh-footer-trust" aria-label="تعهدهای فروشگاه">
  <li>قیمت و موجودی به‌روز</li>
  <li>پیگیری سفارش</li>
  <li>پشتیبانی فروشگاه</li>
 </ul>
 <div class="bh-copy"><span>© <?php echo esc_html(mb_substr(bh_jalali_date(current_time('timestamp')),0,4)); ?> بهاران هوم. همه حقوق محفوظ است.</span><a href="https://sadeqh.ir/" target="_blank" rel="noopener noreferrer">طراح قالب: صادق فلاحی</a></div>
</footer>
<?php $mobile_home=is_front_page();$mobile_shop=(function_exists('is_shop')&&is_shop())||(function_exists('is_product')&&is_product())||(function_exists('is_product_category')&&is_product_category());$mobile_cart=(function_exists('is_cart')&&is_cart())||(function_exists('is_checkout')&&is_checkout());$mobile_account=function_exists('is_account_page')&&is_account_page(); ?>
<nav class="bhp-mobile-nav" aria-label="ناوبری سریع موبایل">
 <a class="<?php echo $mobile_home?'is-current':''; ?>" href="<?php echo esc_url(home_url('/')); ?>" <?php echo $mobile_home?'aria-current="page"':''; ?>><?php echo bh_icon('home'); ?><span>خانه</span></a>
 <a href="<?php echo esc_url(home_url('/#bhb-categories')); ?>"><?php echo bh_icon('search'); ?><span>دسته‌ها</span></a>
 <a class="<?php echo $mobile_shop?'is-current':''; ?>" href="<?php echo esc_url($shop); ?>" <?php echo $mobile_shop?'aria-current="page"':''; ?>><?php echo bh_icon('search'); ?><span>فروشگاه</span></a>
 <a class="<?php echo $mobile_cart?'is-current':''; ?>" href="<?php echo esc_url($cart); ?>" <?php echo $mobile_cart?'aria-current="page"':''; ?>><?php echo bh_icon('cart'); ?><span>سبد</span></a>
 <a class="<?php echo $mobile_account?'is-current':''; ?>" href="<?php echo esc_url($account); ?>" <?php echo $mobile_account?'aria-current="page"':''; ?>><?php echo bh_icon('account'); ?><span>حساب</span></a>
</nav>
<?php wp_footer(); ?>
</body>
</html>
