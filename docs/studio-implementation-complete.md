# بهاران استودیو — پیاده‌سازی اجرایی

## قابلیت‌های اضافه‌شده

- صفحه مستقل «بهاران استودیو» زیر منوی افزونه بهاران
- ترتیب سکشن‌ها با Drag & Drop
- فعال/غیرفعال‌کردن سکشن‌ها
- تنظیم عنوان و زیرعنوان Hero
- انتخاب محصول واقعی Hero از محصولات منتشرشده WooCommerce
- تنظیم تصویر Banner دسکتاپ و موبایل با شناسه رسانه
- Alt Text و URL بنر
- تنظیم عرض Container و مقدار خروج تصویر Hero
- کنترل Motion
- ذخیره با `admin-post.php`، Capability Check و Nonce
- اعمال مستقیم تنظیمات استودیو روی صفحه اصلی از طریق `option_bhc_options`
- استایل مستقل Responsive برای پنل مدیریت

## فایل‌ها

- `wp-content/plugins/baharaan-commerce/includes/studio.php`
- `wp-content/plugins/baharaan-commerce/assets/studio.css`
- `wp-content/plugins/baharaan-commerce/assets/studio.js`
- `wp-content/plugins/baharaan-commerce/baharaan-commerce.php`

## کنترل‌ها

- داده محصول، قیمت و موجودی از WooCommerce خوانده می‌شود.
- رسانه‌ها فقط با شناسه Attachment معتبر استفاده می‌شوند.
- هیچ محصول یا بنر ساختگی ایجاد نمی‌شود.
- PHP lint موفق است.

## محدودیت تست

تست UI واقعی پنل و Screenshot نهایی به اجرای WordPress/Staging نیاز دارد؛ Docker در این محیط قابل اتصال نیست.
