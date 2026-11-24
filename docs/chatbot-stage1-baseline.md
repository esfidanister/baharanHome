# ممیزی و تثبیت مرحله ۱ چت‌بات

## مسیر فعال

- رابط کاربر: `wp-content/plugins/baharaan-commerce/assets/chat.js`
- AJAX action فعال: `bhc_assistant`
- handler فعال: `Baharaan_Commerce::assistant()` در `baharaan-commerce.php`
- nonce رابط کاربر: `bhc_nonce`
- endpoint: `wp-admin/admin-ajax.php`
- مسیر مهمان و کاربر واردشده هر دو با `wp_ajax_nopriv_bhc_assistant` و `wp_ajax_bhc_assistant` ثبت شده‌اند.

## مسیرهای مرتبط

- `bhc_lead`: ثبت درخواست پشتیبانی، با nonce جداگانه و rate limit مستقل.
- `bhc_live_search`: جست‌وجوی عمومی محصولات، با nonce `bhc_public`.
- `bhc_wish_sync`: همگام‌سازی علاقه‌مندی کاربر واردشده، با nonce `bhc_saved`.

## مسیر قدیمی/غیرفعال

`includes/chatbot-v3.php` شامل تابع `bhc_chatbot_v3()` است، اما در فایل اصلی افزونه به‌عنوان handler AJAX ثبت نشده و frontend نیز action دیگری ارسال نمی‌کند. این فایل در مرحله ۱ حذف نمی‌شود؛ تا زمان تصمیم معماری، فقط مسیر مرجع/legacy محسوب می‌شود.

## حفاظت کلید AI

کلید AI در `wp_localize_script` ارسال نمی‌شود. frontend فقط `aiEnabled` و hostname endpoint را دریافت می‌کند؛ درخواست سرویس AI از سمت PHP انجام می‌شود.

## نتیجه تست پایه

- PHP lint فایل افزونه: PASS
- PHP lint مسیر legacy: PASS
- JavaScript syntax check برای `chat.js`: PASS
- JavaScript syntax check برای `chat-enhancements.js`: PASS
- تست runtime واقعی با Playwright در `tools/chat-stage1-runtime.cjs` اجرا شد.
- پیام ساده از UI: PASS؛ پاسخ HTTP 200 و پیام fallback معتبر دریافت شد.
- پیام خالی مستقیم: PASS؛ HTTP 400 و پیام اعتبارسنجی دریافت شد.
- nonce نامعتبر مستقیم: PASS؛ HTTP 403 دریافت شد.
- کلید AI در HTML: PASS؛ `ai_key` و Bearer token در خروجی صفحه مشاهده نشد.
- viewportهای 320، 360، 375، 393، 412، 480، 600، 768، 900، 1024، 1280 و 1440: PASS از نظر نبود horizontal overflow.
- باز و بسته‌کردن پنجره چت و aria-expanded: PASS.
- Console بعد از اصلاح تبدیل `BHI.userId` به عدد: PASS؛ خطای 400 مربوط به همگام‌سازی مهمان حذف شد.
- PHP/JS syntax check: PASS.
- تست صفحه واقعی wp-admin و capability نقش‌ها: هنوز نیازمند اجرای session احراز‌شده است.
