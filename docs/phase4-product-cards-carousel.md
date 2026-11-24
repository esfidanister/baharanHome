# فاز ۴ — Product Card Variants و Carousel System

## اجرا

- ارتفاع کارت‌ها در ریل و گرید پایدار شد و بخش قیمت/CTA به پایین کارت می‌چسبد.
- چهار حالت بصری در Design System تعریف شد: Standard، Compact، Featured و Horizontal.
- حالت‌های Loading با Shimmer، Empty و Disabled اضافه شدند.
- جهت حرکت کنترل‌های Carousel با `direction: rtl` سازگار شد.
- Intersection Observer برای سکشن‌های جدید صفحه خانه نیز فعال شد.
- کارت‌های فعلی همچنان به قیمت، موجودی، افزودن به سبد، مقایسه و علاقه‌مندی واقعی متصل هستند.
- Reduced Motion برای انیمیشن ورود و Shimmer رعایت شد.

## فایل‌های تغییرکرده

- `wp-content/themes/baharaan-home/assets/js/main.js`
- `wp-content/themes/baharaan-home/assets/css/design-system.css`

## اعتبارسنجی

- PHP lint کل قالب و افزونه‌های اختصاصی: موفق.
- منطق WooCommerce و داده‌های محصول تغییر نکرد.
- تست بصری و تعامل واقعی مرورگر به اجرای WordPress runtime وابسته است و Docker در محیط فعلی در دسترس نبود.

## وضعیت فاز

فاز ۴ از نظر سورس تکمیل شد.

## فاز بعدی

فاز ۵ — Shop و Category Experience: بازطراحی آرشیو فروشگاه، فیلترهای واقعی، زیر‌دسته‌های تصویری، Grid/List، Empty State و فیلتر موبایلی Bottom Sheet.
