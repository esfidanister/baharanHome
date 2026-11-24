# فاز ۷ — Banner & Asset System

## اجرا

- اندازه‌های اختصاصی Banner Desktop، Banner Mobile و Editorial ثبت شد.
- helper مرکزی `bh_picture()` برای خروجی Responsive `<picture>` و `<source>` اضافه شد.
- بنر کمپین صفحه اصلی با تصویر دسکتاپ/موبایل، Alt Text و لینک واقعی پیاده شد.
- اگر تصویر کمپین تعریف نشده باشد، هیچ بنر جایگزین یا محتوای ساختگی نمایش داده نمی‌شود.
- `srcset` و `sizes` وردپرس برای تصویر دسکتاپ استفاده می‌شود.
- نسبت‌های تصویری پایدار، Lazy Loading و Decoding Async فعال هستند.
- طراحی بنر با Focus، Hover و Reduced Motion هماهنگ شد.
- مسیر آماده استفاده از WebP/AVIF از طریق Image Editor وردپرس و تنظیمات موجود پروژه حفظ شد.

## فایل‌های تغییرکرده

- `wp-content/themes/baharaan-home/functions.php`
- `wp-content/themes/baharaan-home/front-page.php`
- `wp-content/themes/baharaan-home/assets/css/design-system.css`
- `wp-content/plugins/baharaan-commerce/baharaan-commerce.php`

## اعتبارسنجی

- PHP lint کل قالب و افزونه‌های اختصاصی: موفق.
- داده کمپین فقط در صورت وجود Attachment معتبر نمایش داده می‌شود.
- Screenshot و تست واقعی خروجی تصویر به اجرای WordPress runtime نیاز دارد؛ Docker در محیط فعلی در دسترس نبود.

## وضعیت فاز

فاز ۷ از نظر سورس تکمیل شد.

## فاز بعدی

فاز ۸ — Baharan Design Studio: تکمیل پنل مدیریت سکشن‌ها، بنرها، حالت‌های Hero، تنظیمات Responsive و کنترل امن Import/Export.
