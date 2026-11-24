# فاز ۵ — Shop و Category Experience

## اجرا

- ظاهر آرشیو فروشگاه با Design System آبی و سطح‌های روشن هماهنگ شد.
- Breadcrumb و Empty State بصری برای مسیر فروشگاه تقویت شد.
- فیلتر فعلی WooCommerce برای قیمت، برند، دسته و موجودی حفظ و در موبایل به پنل شناور تبدیل شد.
- دکمه فیلتر موبایل با Escape و حالت باز/بسته قابل کنترل شد.
- Grid/List View به‌صورت progressive enhancement به Toolbar فروشگاه اضافه شد.
- حالت List کارت‌ها برای دسکتاپ و موبایل پیاده شد.
- فیلترها و مرتب‌سازی همچنان از Query واقعی WooCommerce استفاده می‌کنند.
- بدون تغییر در محصولات، قیمت‌ها یا منطق مالی فروشگاه.

## فایل‌های تغییرکرده

- `wp-content/themes/baharaan-home/assets/js/main.js`
- `wp-content/themes/baharaan-home/assets/css/design-system.css`

## اعتبارسنجی

- PHP lint کل قالب و افزونه‌های اختصاصی: موفق.
- JavaScript syntax check: موفق.
- منطق فیلتر موجود WooCommerce حفظ شد.
- Screenshot و تست مرورگر به اجرای WordPress/Docker نیاز دارد؛ Docker در محیط فعلی در دسترس نبود.

## وضعیت فاز

فاز ۵ از نظر سورس تکمیل شد.

## فاز بعدی

فاز ۶ — Product Detail و Purchase Journey: تکمیل گالری، زوم، مشخصات فنی، انتخاب تنوع، CTA خرید، Sticky Purchase Bar موبایل و مسیر خرید Premium.
