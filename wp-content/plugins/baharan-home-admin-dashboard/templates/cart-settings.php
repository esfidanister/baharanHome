<?php defined( 'ABSPATH' ) || exit; settings_errors( 'bh_cart_settings' ); ?>
<div class="wrap bh-cart-settings-page" dir="rtl" lang="fa">
<h1>تنظیمات حرفه‌ای سبد خرید</h1>
<p>تجربهٔ سبد خرید فروشگاه را با کنترل‌های واقعی و قابل بازگشت مدیریت کنید. تغییرات پس از ذخیره در تنظیمات بهاران هوم و تجربهٔ خرید سایت اعمال می‌شوند.</p>
<form method="post">
<input type="hidden" name="bh_cart_settings_nonce" value="<?php echo esc_attr( wp_create_nonce( 'bh_save_cart_settings' ) ); ?>">
<div class="bh-settings-grid">
<section class="bh-settings-card"><h2>تجربه و اعتماد</h2><p class="description">اجزای کمکی سبد خرید برای کاهش تردید و افزایش تکمیل خرید.</p>
<label class="bh-toggle"><input type="checkbox" name="bh_settings[cart_steps]" value="1" <?php checked( $settings['cart_steps'], 1 ); ?>><span><b>نمایش مراحل خرید</b><small>نمایش مسیر سبد، اطلاعات و پرداخت</small></span></label>
<label class="bh-toggle"><input type="checkbox" name="bh_settings[cart_trust]" value="1" <?php checked( $settings['cart_trust'], 1 ); ?>><span><b>نمایش پیام‌های اعتماد</b><small>ارسال امن، پشتیبانی و ضمانت خرید</small></span></label>
<label class="bh-toggle"><input type="checkbox" name="bh_settings[cart_coupon]" value="1" <?php checked( $settings['cart_coupon'], 1 ); ?>><span><b>نمایش کد تخفیف</b><small>ورودی کد تخفیف را در سبد فعال نگه می‌دارد</small></span></label>
<label class="bh-toggle"><input type="checkbox" name="bh_settings[cart_sticky_summary]" value="1" <?php checked( $settings['cart_sticky_summary'], 1 ); ?>><span><b>خلاصهٔ چسبان سفارش</b><small>خلاصه سفارش هنگام اسکرول در دسترس بماند</small></span></label>
</section>
<section class="bh-settings-card"><h2>پیشنهادهای هوشمند</h2><p class="description">محصولات مرتبط را در سبد به‌صورت کنترل‌شده نمایش دهید.</p>
<label class="bh-toggle"><input type="checkbox" name="bh_settings[cart_recommendations]" value="1" <?php checked( $settings['cart_recommendations'], 1 ); ?>><span><b>فعال‌سازی پیشنهاد محصولات</b><small>محصولات مرتبط و مکمل را نمایش می‌دهد</small></span></label>
<label for="cart_recommendation_count"><b>تعداد پیشنهادها</b><small class="description">بین ۲ تا ۸ محصول</small></label>
<input id="cart_recommendation_count" type="number" min="2" max="8" name="bh_settings[cart_recommendation_count]" value="<?php echo esc_attr( $settings['cart_recommendation_count'] ); ?>">
</section>
<section class="bh-settings-card bh-settings-card--wide"><h2>حالت سبد خالی</h2><p class="description">متنی که مشتری پیش از افزودن محصول می‌بیند.</p>
<label for="cart_empty_title">عنوان</label><input id="cart_empty_title" type="text" name="bh_settings[cart_empty_title]" value="<?php echo esc_attr( $settings['cart_empty_title'] ); ?>" maxlength="120">
<label for="cart_empty_text">توضیح</label><textarea id="cart_empty_text" name="bh_settings[cart_empty_text]" rows="4" maxlength="500"><?php echo esc_textarea( $settings['cart_empty_text'] ); ?></textarea>
</section>
</div>
<p class="submit"><button class="button button-primary" type="submit">ذخیره تنظیمات سبد خرید</button> <a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=bh-dashboard' ) ); ?>">بازگشت به داشبورد</a></p>
</form>
<div class="bh-settings-danger-zone"><div><strong>بازنشانی تنظیمات</strong><small>همه گزینه‌های سبد خرید به مقدار استاندارد برمی‌گردند.</small></div><form method="post"><input type="hidden" name="bh_cart_reset_nonce" value="<?php echo esc_attr( wp_create_nonce( 'bh_reset_cart_settings' ) ); ?>"><button class="button" type="submit" onclick="return window.confirm('تنظیمات سبد خرید به مقدار استاندارد بازگردد؟');">بازنشانی استاندارد</button></form></div>
</div>
