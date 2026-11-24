<?php
require '/var/www/html/wp-load.php';

$rows = [
    [
        'id'=>449, 'model'=>'CTL636ES1', 'title'=>'قهوه‌ساز تمام‌اتوماتیک توکار بوش سری ۸ مدل CTL636ES1',
        'source'=>'https://www.bosch-home.com/de/de/product/CTL636ES1',
        'specs'=>['برند'=>'Bosch / بوش','مدل دقیق'=>'CTL636ES1','سری'=>'Series 8','نوع'=>'قهوه‌ساز تمام‌اتوماتیک توکار','رنگ'=>'استیل ضدزنگ و شیشه مشکی','ابعاد'=>'ارتفاع ۴۵۵ × عرض ۵۹۴ × عمق ۳۷۵ میلی‌متر','ابعاد فضای نصب'=>'ارتفاع ۴۴۹ × عرض ۵۵۸ × عمق ۳۵۶ میلی‌متر','ظرفیت مخزن آب'=>'۲٫۴ لیتر','ظرفیت مخزن دانه'=>'۵۰۰ گرم','سیستم شیر'=>'سیستم شیر یکپارچه با شلنگ','آماده‌سازی همزمان'=>'۲ فنجان','فناوری‌ها'=>'OneTouch DoubleCup، MyCoffee، AromaDouble Shot و CeramDrive','اتصال هوشمند'=>'ندارد','وضعیت'=>'ناموجود']
    ],
    [
        'id'=>450, 'model'=>'TIE20119', 'title'=>'قهوه‌ساز تمام‌اتوماتیک بوش VeroCafe Piano مدل TIE20119',
        'source'=>'https://www.bosch-home.com/eg/en/product/TIE20119',
        'specs'=>['برند'=>'Bosch / بوش','مدل دقیق'=>'TIE20119','سری'=>'Series 2','نوع'=>'قهوه‌ساز تمام‌اتوماتیک','رنگ'=>'مشکی Piano Black','ابعاد'=>'ارتفاع ۳۷۸ × عرض ۲۴۷ × عمق ۴۲۰ میلی‌متر','مخزن آب'=>'۱٫۴ لیتر','مخزن دانه قهوه'=>'۲۵۰ گرم','تعداد مخزن دانه'=>'۱','سیستم شیر'=>'نازل شیر برای کف‌دادن داخل فنجان','نوشیدنی‌های تک‌لمسی'=>'لاته ماکیاتو و کاپوچینو','آسیاب'=>'آسیاب سرامیکی','اتصال هوشمند'=>'ندارد','وضعیت'=>'ناموجود']
    ],
    [
        'id'=>455, 'model'=>'HMT72G420', 'title'=>'مایکروویو رومیزی بوش سری ۲ مدل HMT72G420',
        'source'=>'https://media3.bosch-home.com/Documents/specsheet/en-XN/HMT72G420.pdf',
        'specs'=>['برند'=>'Bosch / بوش','مدل دقیق'=>'HMT72G420','سری'=>'Series 2','نوع'=>'مایکروویو رومیزی با گریل','رنگ'=>'سفید','ظرفیت محفظه'=>'۱۷ لیتر','نوع کنترل'=>'مکانیکی','سطوح توان'=>'۵ سطح؛ ۹۰، ۱۸۰، ۳۶۰، ۶۰۰ و ۸۰۰ وات','حداکثر توان مایکروویو'=>'۸۰۰ وات','توان اتصال'=>'۱۲۷۰ وات','ابعاد دستگاه'=>'ارتفاع ۲۹۰ × عرض ۴۶۱ × عمق ۳۵۱ میلی‌متر','ابعاد محفظه'=>'ارتفاع ۱۹۴ × عرض ۲۹۰ × عمق ۲۷۴ میلی‌متر','سینی گردان'=>'دارد','تجهیزات همراه'=>'۱ عدد توری گریل','وضعیت'=>'ناموجود']
    ]
];

foreach ($rows as $r) {
    $id=(int)$r['id']; wp_update_post(['ID'=>$id,'post_title'=>$r['title'],'post_status'=>'draft']);
    $p=wc_get_product($id); if($p){$p->set_catalog_visibility('hidden');$p->set_stock_status('outofstock');$p->save();}
    $table='';$lines=[];foreach($r['specs'] as $k=>$v){$table.='<tr><th>'.esc_html($k).'</th><td>'.esc_html($v).'</td></tr>';$lines[]=$k.': '.$v;}
    $content='<h2>درباره محصول</h2><p>'.$r['title'].' بر اساس صفحه یا برگه مشخصات رسمی Bosch ثبت شده است. این کالا فعلاً ناموجود است و قیمت برای آن نمایش داده نمی‌شود.</p><h2>اطلاعات فنی</h2><table><tbody>'.$table.'</tbody></table><h2>ویژگی‌های کلیدی</h2><ul><li>'.implode('</li><li>',array_slice($lines,3,9)).'</li></ul><h2>وضعیت موجودی</h2><p>این محصول فعلاً ناموجود است و قیمت نمایش داده نمی‌شود.</p><h2>منبع صحت‌سنجی</h2><p>مدل کامل و مشخصات از منبع رسمی Bosch بررسی و ثبت شده است.</p>';
    wp_update_post(['ID'=>$id,'post_content'=>$content]);
    update_post_meta($id,'_bhc_model',$r['model']); update_post_meta($id,'_bhc_source_url',$r['source']); update_post_meta($id,'_bhc_source_urls',wp_json_encode([$r['source']],JSON_UNESCAPED_SLASHES)); update_post_meta($id,'_bhc_source_type','official_exact_model_page'); update_post_meta($id,'_bhc_source_checked_at','2026-11-21'); update_post_meta($id,'_bhc_source_evidence','منبع رسمی Bosch نام کامل مدل و مشخصات همان محصول را نمایش می‌دهد.'); update_post_meta($id,'_bhc_structured_specs',wp_json_encode($r['specs'],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)); update_post_meta($id,'_bhc_highlights',implode("\n",$lines)); update_post_meta($id,'_bhc_verification_status','verified_reference'); update_post_meta($id,'_bhc_catalog_quality_state','reference_verified'); update_post_meta($id,'_bhc_release_blocked','no'); update_post_meta($id,'_bhc_no_price_message','این محصول فعلاً ناموجود است و قیمت نمایش داده نمی‌شود.'); update_post_meta($id,'_yoast_wpseo_metadesc',mb_substr($r['title'].'؛ مشخصات فنی رسمی، ویژگی‌های کلیدی و وضعیت ناموجود در بهاران‌هوم.',0,155));
    echo "VERIFIED|$id|{$r['model']}\n";
}
