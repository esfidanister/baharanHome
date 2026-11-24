# فاز ۳ — Homepage Section Library

## اجرا

- Quick Shopping Entry با دسته‌های واقعی سطح اول ووکامرس.
- Bento Campaign با محصولات دارای تخفیف واقعی؛ در نبود تخفیف، سکشن حذف می‌شود.
- Best Sellers بر اساس popularity ووکامرس؛ در نبود محصول، سکشن حذف می‌شود.
- کارت‌های سکشن‌ها به لینک واقعی محصول/دسته متصل هستند.
- تصاویر از اندازه‌های ثبت‌شده وردپرس با lazy loading استفاده می‌کنند.
- نسخه موبایل برای Quick Entry، Bento و Best Sellers مستقل تنظیم شد.
- کلیدهای جدید به `home_sections` افزوده شدند و از تنظیمات موجود پشتیبانی می‌کنند.
- داده ساختگی، شمارنده جعلی و قیمت ساختگی اضافه نشد.

## فایل‌های تغییرکرده

- `wp-content/themes/baharaan-home/front-page.php`
- `wp-content/themes/baharaan-home/assets/css/design-system.css`
- `wp-content/plugins/baharaan-commerce/baharaan-commerce.php`

## اعتبارسنجی

- PHP lint قالب صفحه خانه و افزونه Commerce موفق بود.
- کوئری‌ها فقط وضعیت `publish` و محصولات واقعی WooCommerce را می‌خوانند.
- نمایش شرطی برای وضعیت خالی در نظر گرفته شد.
- تست مرورگر و Screenshot به دلیل در دسترس نبودن Docker هنوز قابل انجام نیست.

## وضعیت فاز

فاز ۳ از نظر سورس تکمیل شد.

## فاز بعدی

فاز ۴ — Product Card Variants و Carousel System: استانداردسازی چهار نوع کارت محصول، کنترل‌های RTL، حالت‌های Loading/Empty/Disabled و اتصال کامل تعاملات سبد، علاقه‌مندی و مقایسه.
