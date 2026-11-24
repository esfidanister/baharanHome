# داشبورد مدیریتی بهاران هوم

## نصب افزونه

فایل `baharan-home-admin-dashboard-0.2.0.zip` را از مسیر افزونه‌ها > افزودن افزونه > بارگذاری افزونه نصب کنید و سپس فعال نمایید.

## نیازمندی‌ها

- WordPress 6.4 یا جدیدتر
- PHP 7.4 یا جدیدتر
- WooCommerce برای نمایش داده‌های فروش
- مجوز `view_woocommerce_reports`، `manage_woocommerce` یا `manage_options`

## محیط Docker

```powershell
docker compose -f docker-compose.baharanhome.yml up -d
```

داشبورد محلی در `http://localhost:8088/wp-admin/admin.php?page=bh-dashboard` در دسترس است.

## انتقال به cPanel

قبل از انتقال، از پایگاه‌داده و `wp-content/uploads` نسخه پشتیبان بگیرید. در cPanel فقط افزونه مستقل را نصب کنید؛ فایل‌های هسته، قالب و WooCommerce را با این افزونه جایگزین نکنید.

## نکات داده و امنیت

- تاریخ ذخیره‌شده سفارش‌ها تغییر نمی‌کند.
- مبلغ از تنظیمات WooCommerce گرفته می‌شود.
- اطلاعات گزارش فقط برای نقش‌های مجاز نمایش داده می‌شود.
- تنظیمات داشبورد شامل سفارش، کاربر، گذرواژه یا کلید API نیست.
