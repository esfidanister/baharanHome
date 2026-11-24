# فاز ۱۱ — SEO، Schema و Performance نهایی

## اجرا

- Preload تصویر LCP صفحه اصلی برای محصول واقعی Hero اضافه شد.
- `fetchpriority=high` برای تصویر LCP حفظ شد.
- تصاویر Responsive با `srcset`، `sizes`، ابعاد مشخص و Lazy Loading برای محتوای پایین صفحه استفاده می‌شوند.
- canonical، robots، Open Graph و meta description موجود با تشخیص افزونه‌های SEO از Duplicate شدن محافظت می‌شوند.
- Schema محصول WooCommerce حفظ شد و قالب Schema موازی جعلی اضافه نکرد.
- `content-visibility` و `contain` برای بخش‌های پایین صفحه اضافه شد تا هزینه رندر اولیه کاهش یابد.
- نسبت تصویر و ابعاد ثابت برای کاهش CLS تقویت شد.
- هیچ Preloader سنگین، فونت خارجی یا کتابخانه اضافی اضافه نشد.

## اعتبارسنجی

- PHP lint کل قالب و افزونه‌های اختصاصی: موفق.
- منطق SEO افزونه‌های موجود حفظ شد.
- اندازه‌گیری واقعی LCP/INP/CLS و Lighthouse به اجرای سایت در مرورگر نیاز دارد؛ Docker در محیط فعلی در دسترس نبود.

## وضعیت فاز

فاز ۱۱ از نظر سورس تکمیل شد؛ معیارهای Core Web Vitals هنوز نیازمند اجرای واقعی و اندازه‌گیری هستند.

## فاز بعدی

فاز ۱۲ — QA نهایی، Screenshot و Release Package: اجرای تست مرورگر، بررسی Console، ریسپانسیو، مسیر خرید، بسته‌بندی ZIP و مستندات نصب/Backup/Rollback.
