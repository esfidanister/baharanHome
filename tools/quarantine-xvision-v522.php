<?php
require '/var/www/html/wp-load.php';

$rows = [
 ['sku'=>'BHC-X-VISION-XVC-2600','candidates'=>['https://www.10list.ir/معرفی-جاروبرقی-ایکس-ویژن-xvc-2200/'],'reason'=>'کد XVC-2600 در منابع عمومی با منبع دقیق مدل و مشخصات رسمی قابل تطبیق نبود؛ صفحه XVC-2200 فقط کاندید خانواده است.'],
 ['sku'=>'BHC-X-VISION-XVC-2800','candidates'=>['https://www.10list.ir/معرفی-جاروبرقی-ایکس-ویژن-xvc-2200/'],'reason'=>'کد XVC-2800 در منابع عمومی با منبع دقیق مدل و مشخصات رسمی قابل تطبیق نبود؛ صفحه XVC-2200 فقط کاندید خانواده است.'],
 ['sku'=>'BHC-X-VISION-XAC-24000','candidates'=>['https://www.technolife.com/product-311920/کولر-گازی-24-هزار-ایکس-ویژن-مدل-xac-24chsa-hct1'],'reason'=>'ظرفیت 24000 با چند پسوند HCT1 و HUIT3 عرضه شده و SKU فعلی پسوند را مشخص نمی‌کند.'],
 ['sku'=>'BHC-X-VISION-XAC-30000','candidates'=>['https://www.technolife.com/category/home-app/air-conditioner/brand-xvision'],'reason'=>'ظرفیت 30000 با حداقل پسوندهای HCT1 و HUIT3 عرضه شده و SKU فعلی پسوند را مشخص نمی‌کند.'],
 ['sku'=>'BHC-X-VISION-XAC-36000','candidates'=>['https://www.technolife.com/category/home-app/air-conditioner/brand-xvision'],'reason'=>'برای ظرفیت 36000 منبع دقیقِ قابل انتساب به SKU فعلی پیدا نشد.'],
 ['sku'=>'BHC-X-VISION-XAC-48000','candidates'=>['https://www.technolife.com/category/home-app/air-conditioner/brand-xvision'],'reason'=>'برای ظرفیت 48000 منبع دقیقِ قابل انتساب به SKU فعلی پیدا نشد.'],
 ['sku'=>'BHC-X-VISION-XVC-2200','candidates'=>['https://www.10list.ir/معرفی-جاروبرقی-ایکس-ویژن-xvc-2200/'],'reason'=>'منبع فروشگاهی/مقاله‌ای موجود است اما منبع رسمی یا صفحه محصول دقیق و مشخصات کامل قابل راستی‌آزمایی پیدا نشد.'],
 ['sku'=>'BHC-X-VISION-XVC-2400','candidates'=>['https://www.10list.ir/معرفی-جاروبرقی-ایکس-ویژن-xvc-2200/'],'reason'=>'کد XVC-2400 در منبع دقیق و قابل راستی‌آزمایی پیدا نشد؛ صفحه XVC-2200 فقط کاندید خانواده است.'],
 ['sku'=>'BHC-X-VISION-XRF-670','candidates'=>[],'reason'=>'کد دقیق XRF-670 در منبع معتبر قابل راستی‌آزمایی پیدا نشد.'],
 ['sku'=>'BHC-X-VISION-XRF-700','candidates'=>[],'reason'=>'کد دقیق XRF-700 در منبع معتبر قابل راستی‌آزمایی پیدا نشد.'],
 ['sku'=>'BHC-X-VISION-XRF-750','candidates'=>[],'reason'=>'کد دقیق XRF-750 در منبع معتبر قابل راستی‌آزمایی پیدا نشد.'],
 ['sku'=>'BHC-X-VISION-XQW-12000','candidates'=>[],'reason'=>'کد دقیق XQW-12000 در منبع معتبر قابل راستی‌آزمایی پیدا نشد.'],
 ['sku'=>'BHC-X-VISION-XAC-12000','candidates'=>['https://www.technolife.com/product-238514/کولر-گازی-12-هزار-ایکس-ویژن-مدل-xac-12chsa-hct1'],'reason'=>'ظرفیت 12000 با پسوندهای HCT1 و HUIT3 عرضه شده و SKU فعلی پسوند را مشخص نمی‌کند.'],
 ['sku'=>'BHC-X-VISION-XQW-10000','candidates'=>[],'reason'=>'کد دقیق XQW-10000 در منبع معتبر قابل راستی‌آزمایی پیدا نشد.'],
 ['sku'=>'BHC-X-VISION-XRF-650','candidates'=>[],'reason'=>'کد دقیق XRF-650 در منبع معتبر قابل راستی‌آزمایی پیدا نشد.'],
 ['sku'=>'BHC-X-VISION-XRF-800','candidates'=>[],'reason'=>'کد دقیق XRF-800 در منبع معتبر قابل راستی‌آزمایی پیدا نشد.'],
 ['sku'=>'BHC-X-VISION-XRF-850','candidates'=>[],'reason'=>'کد دقیق XRF-850 در منبع معتبر قابل راستی‌آزمایی پیدا نشد.'],
 ['sku'=>'BHC-X-VISION-XK-55XK530','candidates'=>[],'reason'=>'کد XK-55XK530 با صفحه رسمی یا فروشگاهیِ مشخصات کامل قابل تطبیق نشد.'],
 ['sku'=>'BHC-X-VISION-XQW-9400','candidates'=>[],'reason'=>'کد دقیق XQW-9400 در منبع معتبر قابل راستی‌آزمایی پیدا نشد.'],
 ['sku'=>'BHC-X-VISION-XQW-8200','candidates'=>[],'reason'=>'کد دقیق XQW-8200 در منبع معتبر قابل راستی‌آزمایی پیدا نشد.'],
 ['sku'=>'BHC-X-VISION-XQW-7200','candidates'=>[],'reason'=>'کد دقیق XQW-7200 در منبع معتبر قابل راستی‌آزمایی پیدا نشد.'],
 ['sku'=>'BHC-X-VISION-XYU-75XCU665','candidates'=>[],'reason'=>'کد XYU-75XCU665 در منابع عمومی فقط به‌صورت خانواده/نام تجاری دیده شد و مشخصات دقیق مدل قابل راستی‌آزمایی نشد.'],
 ['sku'=>'BHC-X-VISION-XK-43XK530','candidates'=>[],'reason'=>'کد XK-43XK530 با صفحه رسمی یا فروشگاهیِ مشخصات کامل قابل تطبیق نشد.'],
];

foreach ($rows as $row) {
    $id = wc_get_product_id_by_sku($row['sku']);
    if (!$id) { echo "MISSING|{$row['sku']}\n"; continue; }
    $p = wc_get_product($id);
    if ($p) { $p->set_catalog_visibility('hidden'); $p->set_stock_status('outofstock'); $p->save(); }
    update_post_meta($id, '_bhc_source_candidates', wp_json_encode($row['candidates'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
    update_post_meta($id, '_bhc_source_type', 'family_suffix_needs_exact');
    update_post_meta($id, '_bhc_source_checked_at', '2026-11-21');
    update_post_meta($id, '_bhc_source_evidence', $row['reason']);
    update_post_meta($id, '_bhc_verification_status', 'pending_exact_model_source');
    update_post_meta($id, '_bhc_catalog_quality_state', 'reference_needs_verification');
    update_post_meta($id, '_bhc_release_blocked', 'yes');
    update_post_meta($id, '_bhc_no_price_message', 'این محصول فعلاً ناموجود است و تا تأیید مدل و مشخصات دقیق عرضه نمی‌شود.');
    echo "QUARANTINED|{$row['sku']}|{$id}\n";
}
