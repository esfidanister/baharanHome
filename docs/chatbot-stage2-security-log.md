# گزارش پیشرفت مرحله ۲ — امنیت و حریم خصوصی

## اصلاحات اجراشده

- handler امن `bhc_lead_secure` اضافه شد.
- ذخیره contact بدون رضایت جداگانه رد می‌شود.
- پیام‌های حاوی CVV، OTP، رمز، شماره کارت و password رد می‌شوند.
- برای leadهای جدید تاریخ انقضای ۹۰ روزه ثبت می‌شود.
- پاک‌سازی روزانه leadهای منقضی با WP-Cron اضافه شد.
- فرم چت برای contact یک رضایت جداگانه نمایش می‌دهد.
- درخواست قدیمی `bhc_lead` در frontend به handler امن هدایت می‌شود.
- متن پیام و contact قبل از ذخیره sanitize می‌شوند.
- contact خام در متادیتای عادی ذخیره نمی‌شود؛ مقدار رمزنگاری‌شده و مقدار maskشده جدا هستند.
- هنگام ثبت lead یک توکن خصوصی حذف صادر می‌شود.
- endpoint `bhc_privacy_delete` حذف درخواست و contact را با توکن خصوصی انجام می‌دهد.
- endpoint `bhc_privacy_export` export کنترل‌شده داده را با همان توکن فراهم می‌کند.
- پاک‌سازی retention علاوه بر WP-Cron، fallback idempotent روی درخواست‌های عادی سایت دارد.
- کلید rate limit برای lead و order از IP-only به ترکیب hash‌شده user ID + IP + user-agent ارتقا یافت (`bhc_rate_subject`)؛ این راهکار اثر کاربران یک شبکه مشترک را کاهش می‌دهد، اما جایگزین WAF/device reputation نیست.

## تست‌های انجام‌شده

- contact بدون رضایت: PASS — HTTP 400
- پیام حاوی شماره کارت: PASS — HTTP 400
- نمایش checkbox رضایت contact در UI: PASS
- پیام معمولی چت پس از تغییرات: PASS — HTTP 200
- Console و viewport regression: PASS
- PHP/JavaScript syntax: PASS
- WooCommerce order ownership: PASS — a real `wc_create_order()` test order was accessible with the matching billing email and rejected with a different email; response contained only status metadata and no address/phone/payment details.
- Lead CPT permissions tightened to dedicated `bhc_leads` capabilities; the seeded `shop_manager` role now has `edit_bhc_leads` and `read_private_bhc_leads`, verified through WordPress bootstrap.

## موارد باقی‌مانده مرحله ۲

- rate-limit عملیاتی PASS شد: پس از ۷ درخواست پذیرفته‌شده، درخواست بعدی با HTTP 429 و پیام فارسی محدودسازی رد شد؛ leadها و transientهای آزمایشی پاک شدند.
- rate-limit مسیر AI نیز به `bhc_rate_subject` ارتقا یافت و دیگر IP-only نیست.
- مسیر AI دارای دفاع ایستا در prompt است: catalog به‌عنوان داده غیرقابل‌اعتماد علامت‌گذاری شده، دستورهای داخل آن نادیده گرفته می‌شوند، پاسخ فقط از context فروشگاه مجاز است، secrets/OTP درخواست نمی‌شود و خروجی sanitize می‌شود. تست injection با AI خاموش، fallback امن و بدون افشای prompt را PASS کرد؛ تست مدل خارجی با کلید فعال هنوز قابل اجرا نیست.
- تست فشار عملیاتی rate limit با درخواست‌های متوالی از یک کلاینت هنوز ثبت نشده است؛ منطق و مسیر کد بازبینی شده است.
- تست واقعی XSS و prompt injection با AI فعال.
- محدودسازی مشاهده leadها برای نقش‌های مدیریتی.
- رابط UI برای export/delete اضافه شد؛ privacy token فقط در `sessionStorage` مرورگر نگه‌داری می‌شود و کنترل‌ها پس از ثبت lead یا refresh همان مرورگر نمایش داده می‌شوند.
