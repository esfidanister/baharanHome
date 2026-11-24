# بهاران هوم — راهنمای Release، نصب و بازگشت

## محتویات بسته

- قالب `baharaan-home`
- افزونه `baharaan-commerce`
- افزونه `baharan-home-admin-dashboard`
- مستندات فازها و گزارش QA

## نصب امن

1. از دیتابیس و `wp-content/uploads` نسخه پشتیبان بگیرید.
2. روی Staging نصب کنید؛ مستقیماً روی Production جایگزین نکنید.
3. افزونه‌ها را از بخش Plugins نصب و فعال کنید.
4. قالب را از بخش Themes نصب و فعال کنید.
5. تنظیمات WooCommerce، پیوندهای یکتا، صفحات Shop/Cart/Checkout/My Account و منوها را بررسی کنید.
6. از پنل بهاران، تنظیمات را بررسی و در صورت نیاز Import کنید.

## Rollback

- قالب و افزونه‌های همین Release را با نسخه قبلی جایگزین کنید.
- تنظیمات JSON قبلی را از Baharan Commerce درون‌ریزی کنید.
- در صورت تغییر داده یا تنظیمات WooCommerce، Backup دیتابیس را فقط پس از تأیید زمان Snapshot بازگردانید.
- WordPress Core و WooCommerce Core نباید از این بسته جایگزین شوند.

## کنترل انتشار

- هیچ محصول، قیمت، سفارش یا تصویر واقعی حذف یا تولید نشده است.
- بسته Release برای نصب Production فقط پس از تست روی Staging و اجرای Browser QA کامل قابل اعلام نهایی است.
