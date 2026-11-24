<?php
require '/var/www/html/wp-load.php';

$items = [
    'BHC-EMERSUN-EF10' => ['model' => 'EF10', 'note' => 'در جست‌وجوی عمومی و فروشگاه‌های بررسی‌شده، صفحه رسمی یا صفحه فروش معتبرِ دقیق همین مدل پیدا نشد؛ مشخصات فنی قابل استناد وارد نشده است.'],
    'BHC-EMERSUN-EF11' => ['model' => 'EF11', 'note' => 'در جست‌وجوی عمومی و فروشگاه‌های بررسی‌شده، صفحه رسمی یا صفحه فروش معتبرِ دقیق همین مدل پیدا نشد؛ مشخصات فنی قابل استناد وارد نشده است.'],
    'BHC-EMERSUN-EF12' => ['model' => 'EF12', 'note' => 'در جست‌وجوی عمومی و فروشگاه‌های بررسی‌شده، صفحه رسمی یا صفحه فروش معتبرِ دقیق همین مدل پیدا نشد؛ مشخصات فنی قابل استناد وارد نشده است.'],
    'BHC-EMERSUN-EF13' => ['model' => 'EF13', 'note' => 'نتایج عمومی مدل EF13 به رکوردهای تعمیراتی با کاربرد نامشخص اشاره دارند و منبع دقیق محصول پیدا نشد؛ دسته‌بندی اجاق و فر تا تطبیق دفترچه یا صفحه سازنده قطعی نیست.'],
];

$checked = 0;
foreach ($items as $sku => $item) {
    $id = wc_get_product_id_by_sku($sku);
    if (!$id) { echo "MISSING|$sku\n"; continue; }
    $p = wc_get_product($id);
    if (!$p) { echo "INVALID|$sku|$id\n"; continue; }
    $p->set_status('draft');
    $p->set_catalog_visibility('hidden');
    $p->set_stock_status('outofstock');
    $p->set_description(
        '<p>' . esc_html($p->get_name()) . ' در کاتالوگ بهاران‌هوم ناموجود و غیرقابل انتشار است.</p>' .
        '<h3>درباره محصول</h3><p>این رکورد از کاتالوگ قدیمی حفظ شده، اما منبع دقیق مدل‌نامبر هنوز تأیید نشده است. برای جلوگیری از ساخت اطلاعات نادرست، ویژگی‌های فنی تأییدنشده حذف و از حدس‌زدن بر اساس شباهت مدل خودداری شده است.</p>' .
        '<h3>اطلاعات فنی</h3><p>پس از یافتن صفحه دقیق سازنده، دفترچه راهنما یا سند معتبر همان مدل تکمیل می‌شود. تا آن زمان ظرفیت، ابعاد، توان، نوع سوخت، امکانات و اقلام همراه قطعی اعلام نمی‌شوند.</p>' .
        '<h3>نتیجه ممیزی منبع</h3><p>' . esc_html($item['note']) . '</p>' .
        '<h3>وضعیت انتشار</h3><p>ناموجود، پیش‌نویس و مخفی از کاتالوگ عمومی. این رکورد تا ثبت منبع دقیق، مشخصات قابل استناد و تطبیق تصویر آزاد نمی‌شود.</p>'
    );
    $p->set_short_description($p->get_name() . ' · ناموجود · در انتظار منبع دقیق مدل');
    $p->save();
    update_post_meta($id, '_bhc_source_url', '');
    update_post_meta($id, '_bhc_source_type', 'legacy_needs_exact');
    update_post_meta($id, '_bhc_source_checked_at', '2026-11-21');
    update_post_meta($id, '_bhc_source_review_notes', 'فاز ۵۰۱: جست‌وجوی منبع دقیق مدل در سایت سازنده و فروشگاه‌های ایرانی انجام شد؛ نتیجه برای انتشار کافی نیست.');
    update_post_meta($id, '_bhc_source_gap_reason', $item['note']);
    update_post_meta($id, '_bhc_verification_status', 'pending_source');
    update_post_meta($id, '_bhc_catalog_quality_state', 'reference_needs_verification');
    update_post_meta($id, '_bhc_release_blocked', 'yes');
    update_post_meta($id, '_bhc_image_generation_status', 'generated_4k_transparent_pending_exact_source');
    echo "AUDITED|$sku|$id|source_unresolved\n";
    $checked++;
}
update_option('bhc_phase_501_emersun_exact_source_audit', ['checked' => $checked, 'at' => current_time('mysql'), 'result' => 'unresolved_exact_sources'], false);
echo "CHECKED|$checked\n";
