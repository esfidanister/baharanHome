<?php
if (!defined('ABSPATH')) { exit; }
add_action('init', function () {
    if (!function_exists('wc_get_product') || get_option('bhc_catalog_bosch_source_gap_v396_done')) return;
    $items=[
        455=>['reason'=>'کد HMT72 بدون پسوند کامل E-Nr است؛ صفحه یا دفترچه رسمی قابل انتساب به یک زیرمدل دقیق در این بررسی پیدا نشد.','family'=>'خانواده Bosch HMT72'],
        454=>['reason'=>'منبع رسمی BFL634 چند زیرمدل مانند BFL634GB1، BFL634GS1 و BFL634GW1 با رنگ و بازار متفاوت دارد؛ کد BFL634 به‌تنهایی برای انتشار مدل دقیق کافی نیست.','family'=>'خانواده Bosch BFL634','source'=>'https://www.bosch-home.com/eg/en/product/BFL634GB1'],
        453=>['reason'=>'منبع رسمی BFL523 چند زیرمدل و رنگ متفاوت دارد؛ کد BFL523 بدون پسوند کامل برای تعیین مشخصات و تصویر دقیق کافی نیست.','family'=>'خانواده Bosch BFL523','source'=>'https://www.bosch-home.com/se/sv/product/spis-ugn-hall-och-flakt/mikrovagsugnar/inbyggda-mikrovagsugnar/BFL523MS0'],
        452=>['reason'=>'منابع رسمی TAS140 را به زیرمدل‌های TAS1401، TAS1402، TAS1403، TAS1404 و TAS1407 با رنگ‌های متفاوت تفکیک می‌کنند؛ پسوند دقیق ثبت نشده است.','family'=>'خانواده Bosch TASSIMO VIVY','source'=>'https://media3.bosch-home.com/Documents/specsheet/de-AT/TAS1402.pdf'],
        451=>['reason'=>'کد TIS857 بدون پسوند کامل E-Nr است و مشخصات یک زیرمدل دقیق از منبع رسمی قابل تعیین نشد.','family'=>'خانواده Bosch TIS857'],
        450=>['reason'=>'کد TIE201 بدون پسوند کامل E-Nr است؛ صفحه رسمی نتایج خانواده را نشان می‌دهد اما SKU دقیق مشخص نیست.','family'=>'خانواده Bosch TIE201','source'=>'https://www.bosch-home.com/ne/en/mkt-product/coffee-machines/fully-automatic-coffee-machines/TIE20119'],
        448=>['reason'=>'کد TIS303 بدون پسوند کامل E-Nr است و منبع رسمی قابل انتساب به یک رنگ/زیرمدل دقیق ثبت نشد.','family'=>'خانواده Bosch TIS303'],
        449=>['reason'=>'کد CTL636 بدون پسوند کامل E-Nr است؛ منابع رسمی آن را به خانواده CTL636... ارجاع می‌دهند و برای مدل دقیق پسوند لازم است.','family'=>'خانواده Bosch CTL636','source'=>'https://www.bosch-home.com/de/de/product/00311965'],
        444=>['reason'=>'کد BGS21 در دفترچه رسمی همراه BGC21 به‌صورت خانواده آمده و زیرمدل/منطقه کامل در کاتالوگ فعلی ثبت نشده است.','family'=>'خانواده Bosch BGS21','source'=>'https://media3.bosch-home.com/Documents/8001233841_A.pdf'],
        442=>['reason'=>'کد BGL6 خانواده‌ای از جاروبرقی‌های کیسه‌دار است و برای مشخصات کامل، پسوند E-Nr لازم است.','family'=>'خانواده Bosch BGL6','source'=>'https://media3.bosch-home.com/Documents/8001350984_C.pdf'],
        441=>['reason'=>'کد BGL8 خانواده‌ای از محصولات In\'genius است و منبع رسمی مدل‌های متعدد BGL8... را نشان می‌دهد؛ پسوند دقیق ثبت نشده است.','family'=>'خانواده Bosch BGL8','source'=>'https://media3.bosch-home.com/Documents/8001002946_A.pdf'],
        443=>['reason'=>'کد BGC05 بدون پسوند کامل E-Nr است و برای تعیین دقیق ظرفیت، رنگ و مشخصات نسخه لازم است.','family'=>'خانواده Bosch BGC05'],
        446=>['reason'=>'کد BGS41 بدون پسوند کامل E-Nr است؛ دفترچه رسمی خانواده BGS41/BGC41 را پوشش می‌دهد اما SKU نهایی مشخص نیست.','family'=>'خانواده Bosch BGS41','source'=>'https://media3.bosch-home.com/Documents/8001251513_A.pdf'],
        447=>['reason'=>'کد TIS301 بدون پسوند کامل E-Nr است و صفحه رسمی یک زیرمدل قطعی برای رکورد فعلی نشان نداد.','family'=>'خانواده Bosch TIS301'],
    ];
    $done=[];
    foreach($items as $id=>$item){$p=wc_get_product($id);if(!$p)continue;$p->set_status('draft');$p->set_catalog_visibility('hidden');$p->set_stock_status('outofstock');$p->save();update_post_meta($id,'_bhc_catalog_quality_state','reference_needs_verification');update_post_meta($id,'_bhc_verification_status','pending_source');update_post_meta($id,'_bhc_release_blocked','yes');update_post_meta($id,'_bhc_source_gap_reason',$item['reason']);update_post_meta($id,'_bhc_candidate_family',$item['family']);update_post_meta($id,'_bhc_source_review_notes','فاز ۳۹۶: کد محصول در کاتالوگ فعلی پسوند کامل E-Nr ندارد. تا دریافت پسوند دقیق از پلاک محصول/دفترچه/صفحه رسمی، انتشار، مشخصات مدل‌محور و تصویر اختصاصی ممنوع است.');if(!empty($item['source']))update_post_meta($id,'_bhc_candidate_family_source',$item['source']);$done[]=$id;}
    update_option('bhc_catalog_bosch_source_gap_v396_done',['quarantined'=>$done,'at'=>current_time('mysql')],false);
});
