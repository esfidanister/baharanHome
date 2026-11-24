<?php
if (!defined('ABSPATH')) { exit; }
add_action('init', function () {
    if (!function_exists('wc_get_product') || get_option('bhc_catalog_bosch_deduplicate_v397_done')) return;
    $duplicate=453; $canonical=1088;
    $p=wc_get_product($duplicate); $c=wc_get_product($canonical);
    if($p && $c){
        $p->set_status('draft'); $p->set_catalog_visibility('hidden'); $p->set_stock_status('outofstock'); $p->save();
        update_post_meta($duplicate,'_bhc_duplicate_of',$canonical);
        update_post_meta($duplicate,'_bhc_source_gap_reason','این رکورد عمومی BFL523 به‌دلیل نداشتن پسوند دقیق با رکورد رسمی BFL523MS0B هم‌پوشانی دارد؛ رکورد دقیق BFL523MS0B با SKU کامل و منبع رسمی در محصول ۱۰۸۸ نگهداری می‌شود.');
        update_post_meta($duplicate,'_bhc_source_review_notes','فاز ۳۹۷: رکورد عمومی به رکورد دقیق موجود لینک شد؛ انتشار هم‌زمان دو رکورد برای جلوگیری از محتوای تکراری و ابهام مدل ممنوع است.');
        update_post_meta($duplicate,'_bhc_catalog_quality_state','reference_needs_verification'); update_post_meta($duplicate,'_bhc_verification_status','duplicate_pending_merge'); update_post_meta($duplicate,'_bhc_release_blocked','yes');
        update_post_meta($canonical,'_bhc_canonical_record','yes'); update_post_meta($canonical,'_bhc_duplicate_source_ids',[$duplicate]);
    }
    update_option('bhc_catalog_bosch_deduplicate_v397_done',['duplicate'=>$duplicate,'canonical'=>$canonical,'at'=>current_time('mysql')],false);
});
