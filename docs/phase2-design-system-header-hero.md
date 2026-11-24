# فاز ۲ — Design System، هدر و Hero

## تغییرات انجام‌شده

- Design Tokens اصلی برای Navy، Blue، Ice Blue، Background، Orange کمپین و عرض Container تثبیت شد.
- Utility Bar اکنون فقط لینک‌های واقعیِ صفحات منتشرشده را نشان می‌دهد؛ لینک ساختگی اضافه نمی‌شود.
- هدر دسکتاپ به ساختار سه‌لایه وایرفریم نزدیک شد: Utility Bar، Main Header و Mega Navigation.
- جست‌وجو، حساب کاربری و سبد خرید واقعی WooCommerce حفظ شدند.
- حالت‌های Hover، Focus، Responsive و Reduced Motion لایه‌بندی شدند.
- Mega Menu در دسکتاپ پنل شناور و در موبایل رفتار Drawer/اسکرولی موجود خود را حفظ می‌کند.
- Hero به ترکیب آبی روشن، CTA آبی، Accent نارنجی کمپینی، ویترین محصول زنده و کارت اطلاعات محصول نزدیک شد.
- در موبایل Hero به چیدمان مستقل دو ردیفه تبدیل شد؛ صرفاً کوچک‌سازی دسکتاپ نیست.
- هیچ محصول، قیمت، موجودی یا ادعای خدماتی تولید نشد.

## فایل‌های تغییرکرده

- `wp-content/themes/baharaan-home/header.php`
- `wp-content/themes/baharaan-home/assets/css/design-system.css`

## اعتبارسنجی

- PHP lint برای قالب و افزونه‌های اختصاصی موفق بود.
- منطق داده و مسیرهای WooCommerce دست‌نخورده باقی ماند.
- تست بصری مرورگر و Screenshot واقعی به اجرای WordPress/Docker وابسته است؛ Docker در این محیط با خطای دسترسی اجرا نشد.

## وضعیت

فاز ۲ از نظر سورس تکمیل شد؛ تأیید نهایی بصری در محیط اجرا باقی است.

## فاز بعدی پیشنهادی

فاز ۳: Hero و Homepage Section Library — پیاده‌سازی Bento Campaign، Shop by Room، Limited Offers، Best Sellers و Banner Studio با داده واقعی و تنظیمات قابل کنترل.
