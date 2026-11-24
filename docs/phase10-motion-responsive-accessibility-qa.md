# فاز ۱۰ — Motion، Responsive و Accessibility QA

## اجرا

- Focus Visible یکپارچه برای لینک‌ها، دکمه‌ها، فرم‌ها و Summaryها اضافه شد.
- Skip Link موجود با حالت Focus قابل‌مشاهده و دسترسی بهتر تقویت شد.
- فیلتر موبایل با Focus اولیه و بازگشت Focus به Trigger تکمیل شد.
- Lightbox محصول با `role=dialog`، `aria-modal`، Escape و بازگشت Focus به دکمه بازکننده تکمیل شد.
- Reduced Motion برای انیمیشن‌ها، Transitionها و Scroll Behavior اعمال شد.
- نقاط شکست ویژه ۳۹۰ پیکسل برای جلوگیری از Overflow و فشردگی بیش از حد اضافه شد.
- کارت‌ها، ریل‌ها و Gridها برای متن‌های طولانی و عرض محدود مقاوم‌تر شدند.
- Scrollbar و تعامل Keyboard برای ریل محصول بهبود یافت.

## اعتبارسنجی

- PHP lint کل قالب و افزونه‌های اختصاصی: موفق.
- JavaScript syntax check برای `main.js` و `product.js`: موفق.
- بررسی سورس برای Focus، Escape، Reduced Motion و نقاط شکست انجام شد.
- تست Screenshot و Playwright واقعی به اجرای WordPress/Docker نیاز دارد؛ Docker در محیط فعلی در دسترس نبود.

## وضعیت فاز

فاز ۱۰ تکمیل شد.

## فاز بعدی

فاز ۱۱ — SEO، Accessibility و Performance نهایی: Schema، متادیتا، تصاویر LCP، Cache، کاهش CLS، بهینه‌سازی Assetها و کنترل نهایی Core Web Vitals.
