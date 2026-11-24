# اجرای محیط Docker بهاران هوم

## اجرا

از ریشه پروژه اجرا کنید:

```powershell
docker compose -f docker-compose.baharanhome.yml up -d
```

سپس آدرس زیر را باز کنید:

`http://localhost:8088`

در نصب اولیه، اطلاعات اتصال پایگاه‌داده از قبل در Compose تنظیم شده است. این رمزها فقط برای محیط محلی هستند و نباید برای cPanel یا محیط production استفاده شوند.

## ورود افزونه

بعد از نصب وردپرس، از مسیر افزونه‌ها، افزونه `Baharan Home Admin Dashboard` را فعال کنید. صفحه داشبورد از منوی «بهاران هوم» در دسترس است.

برای تست داده‌های واقعی فروش، WooCommerce باید داخل همین نصب نصب و فعال شود. این محیط هنوز داده تولیدی فروشگاه ندارد.

## توقف و نگهداری داده

```powershell
docker compose -f docker-compose.baharanhome.yml down
```

داده پایگاه‌داده در volume به نام `baharanhome_db` نگهداری می‌شود. برای حذف داده محلی، فقط پس از اطمینان از بی‌نیازی:

```powershell
docker compose -f docker-compose.baharanhome.yml down -v
```

این دستور اطلاعات محلی پایگاه‌داده را حذف می‌کند و برای محیط واقعی استفاده نشود.
