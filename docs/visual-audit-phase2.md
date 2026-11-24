# ممیزی بصری و فنی — فاز دوم

تاریخ ممیزی: ۱۴۰۵/۰۸/۲۷

## خروجی‌های تأییدشده

- صفحه‌ی خانه در موبایل: hero، دسته‌ها، کارت محصول و bottom navigation رندر شد.
- archive فروشگاه در دسکتاپ: hero دسته، rail دسته‌ها، فیلتر، toolbar و کارت‌ها رندر شد.
- archive در عرض باریک: منوی موبایل، گرید دو ستونه، فیلتر تمام‌عرض و جلوگیری از clipping بررسی شد.
- حالت‌های grid و list محصولات با کنترل واقعی مرورگر تست شدند.
- drawer فیلتر با backdrop و بستن از طریق کنترل close تست شد.
- مسیر محصول، سبد خرید، checkout و حساب کاربری با HTTP 200 و asset نهایی بررسی شدند.
- PHP، JavaScript و توازن braceهای CSS بدون خطا هستند.

## اصلاحات این فاز

- اضافه شدن لایه‌ی نهایی `premium-polish.css` بعد از تمام لایه‌های قدیمی.
- اصلاح breakpointهای ۶۴۰ تا ۹۰۰ پیکسل برای archive فروشگاه.
- حذف pseudo-element منوی تکراری در categoryهای موبایل.
- تبدیل دکمه‌ی فیلتر به کنترل تمام‌عرض در layoutهای باریک.
- یکپارچه‌سازی رنگ، radius، سایه، hover، CTA و motion با مرجع آبی/نارنجی وایرفریم.
- رعایت `prefers-reduced-motion` برای حرکت‌های رابط.
- هماهنگ‌سازی ویجت پشتیبانی با همان زبان بصری سایت در دسکتاپ و موبایل.
- نسخه‌ی نهایی لایه‌ی بصری در این ممیزی `premium-polish.css` با revision `.13` ثبت شد.
- در revision `.9` صفحات سبد خرید، پرداخت و حساب کاربری نیز با همان سیستم کارت، CTA، فرم، فاصله‌گذاری و responsive styling پوشش داده شدند.
- در revision `.10` حلقه‌ی فوکوس واضح و یکپارچه برای لینک‌ها، دکمه‌ها، فرم‌ها و summaryهای تعاملی اضافه شد تا تجربه‌ی کیبورد و دسترس‌پذیری با ظاهر جدید هماهنگ بماند.
- در revision `.11` noticeهای WooCommerce برای خطا، اطلاع و موفقیت نیز با کارت‌ها، رنگ‌های وضعیت، فاصله‌گذاری و آیکون‌های همان سیستم بصری هماهنگ شدند.
- route محصول با HTTP 200 و هر ۷ اسکریپت با `node --check` تأیید شدند؛ lightbox تصویر محصول نیز برای focus trap کیبورد، قفل اسکرول پس‌زمینه و بازگرداندن focus به کنترل آغازکننده تقویت شد.
- در ممیزی کد صفحه محصول مشخص شد thumbnailها پیش از این فقط وضعیت دسترسی را عوض می‌کردند؛ تعویض واقعی تصویر اصلی، active state، alt متناظر و preload تصویر مقصد اکنون در `product.js` پیاده‌سازی و با syntax check تأیید شد.
- duplication تعامل محصول بین `main.js` و `product.js` حذف شد؛ اکنون گالری و lightbox فقط یک بار توسط `product.js` initialize می‌شوند و version assetهای هر دو اسکریپت bump شده است.
- live search نیز هنگام درخواست، `aria-busy` اعلام می‌کند و هنگام بسته‌شدن، `aria-activedescendant` را پاک می‌کند؛ version asset جستجو هم bump شد.
- empty stateهای فروشگاه/خانه و صفحه‌ی 404 در revision `.12` به کارت‌های premium با CTA، کنتراست و فضای تنفس یکپارچه تبدیل شدند.
- route ناشناخته با HTTP 404 و جستجوی محصول پس از redirect با HTTP 200 تأیید شد؛ revision runtime لایه‌ی CSS نیز `1.3.0.blueprint.31.13` مشاهده شد.
- regression audit نهایی این دور: هر ۷ JS با `node --check` سالم، `main.js` بدون selector/logic تکراری گالری محصول، PHPهای اصلی بدون syntax error، CSS با توازن `264/264` و routeهای root/shop/cart/account همگی HTTP 200 هستند.
- runtime audit تکرارشده نیز تأیید کرد `product.js` فقط روی صفحه محصول load می‌شود، root آن را load نمی‌کند، markup lightbox تکراری وجود ندارد، صفحه محصول HTTP 200 و route ناشناخته HTTP 404 باقی مانده است.
- syntax audit گسترده‌تر روی هر ۲۵ فایل PHP theme نیز بدون خطا بود؛ حجم فعلی لایه‌ی premium CSS حدود ۳۱.۸KB است و revision runtime `.13` است.
- لینک «دسته‌ها» در bottom navigation برای صفحات داخلی اصلاح شد تا از cart/checkout/account نیز به `/#bhb-categories` صفحه اصلی برود و anchor بی‌اثر در همان صفحه باقی نماند.
- bottom navigation اکنون صفحه‌ی فعال خانه/فروشگاه/سبد/حساب را با کلاس بصری و `aria-current="page"` مشخص می‌کند؛ ظاهر active با revision `.13` یکپارچه شد.
- runtime check روی shop و account نیز برای هر صفحه دقیقاً یک active item در bottom navigation نشان داد.
- active فروشگاه برای صفحات product و product-category نیز تکمیل شد؛ پیش از این روی صفحه جزئیات محصول هیچ `aria-current` فعالی وجود نداشت.
- category «نظافت» نیز با HTTP 200 و یک active item فروشگاه در bottom navigation تأیید شد.
- checkout نیز اکنون تب «سبد» را به‌عنوان مرحله‌ی فعال مسیر خرید نشان می‌دهد؛ edge case صفحه پرداخت بدون active navigation حذف شد.
- با session موقت WooCommerce، افزودن محصول و checkout واقعی هر دو HTTP 200 شدند و checkout دقیقاً یک active item روی تب «سبد» داشت.
- redirect اولیه‌ی `/checkout/` در تست بدون session خرید، رفتار استاندارد سبد خالی WooCommerce بود؛ پس از افزودن محصول به session، checkout با HTTP 200 باز شد و مشکل به قالب یا CSS نسبت داده نشد.
- بازبینی تصویری زنده‌ی آرشیو دسته‌ی «صوتی و تصویری» نیز انجام شد: هدر، جستجو، breadcrumb، hero دسته، rail دسته‌ها، فیلتر، grid/list controls، کارت محصول، footer و mobile navigation در DOM قابل مشاهده و بدون overflow آشکار بودند.
- بازبینی تصویری mobile homepage در عرض باریک نیز انجام شد: header، cart badge، search، hero، CTA، category cards، bottom navigation و support launcher در قاب قابل مشاهده بودند و شکست یا overflow آشکار نداشتند.
- در کنترل تکمیلی mobile، لینک cart از نظر hover/status bar به مسیر `/cart/` resolve شد؛ نشست Chrome در همان صفحه‌ی خانه باقی ماند و این رفتار automation به‌عنوان خطای سایت ثبت نشد. سلامت route پیش‌تر با HTTP 200 مستقل تأیید شده است.
- بازبینی تصویری زنده‌ی سبد خرید در mobile نیز انجام شد: محصول، تصویر، مبلغ، quantity controls، حذف، مراحل خرید و CTA پرداخت در قاب قابل خواندن بودند و overflow آشکار دیده نشد.
- بازبینی تصویری زنده‌ی حساب کاربری و checkout در mobile انجام شد: کارت معرفی حساب، فرم ورود، خلاصه سفارش، فرم اطلاعات تماس، فیلدهای صورتحساب و bottom navigation رندر خوانا داشتند. در محیط محلی، WooCommerce پیام «هیچ روش پرداختی در دسترس نیست» نشان می‌دهد؛ این تنظیمات درگاه است و برای تست سفارش واقعی باید در staging/production پیکربندی شود، نه اینکه با CSS پنهان شود.
- راستی‌آزمایی HTML زنده‌ی صفحه محصول نیز نشان داد لینک «دسته‌ها» از صفحات داخلی به `/#bhb-categories` resolve می‌شود؛ مشاهده‌ی مسیر قدیمی در تب Chrome ناشی از cache/تب قدیمی بود.
- آخرین regression routeها در همین دور: خانه، فروشگاه، سبد، حساب کاربری، محصول و دسته‌ی «نظافت» همگی `HTTP 200` و مسیر ناشناخته `HTTP 404` بازگرداندند.
- در ممیزی محصول تک‌تصویری یک early return در `product.js` پیدا و حذف شد؛ اکنون نبود thumbnail هم مانع فعال‌شدن sticky خرید و lightbox تصویر نمی‌شود و revision اسکریپت محصول به `.10` bump شد.
- تب‌های ورود/ثبت‌نام حساب نیز از نظر دسترس‌پذیری تکمیل شد: `aria-controls`، `tabpanel`، roving tabindex و حرکت با کلیدهای چپ/راست اضافه شد.
- workaround قدیمی PHP که در صفحات دسته‌بندی شمارنده‌ی محصولات و launcher پشتیبانی را مخفی می‌کرد حذف شد؛ یک guard CSS قدیمی در `shop.css` نیز کشف و با override صریح در آخرین لایه‌ی premium خنثی شد تا هر دو عنصر واقعاً visible و interactive باشند.
- HTML زنده‌ی archive «نظافت» هر دو عنصر را دارد؛ visibility نهایی اکنون در `premium-polish.css` با `display/visibility/opacity/pointer-events` صریح تثبیت شده و PHP فایل اصلی بدون خطا lint شد.
- ساختار lightbox محصول از `innerHTML` به DOM API امن منتقل شد تا alt/titleهای دارای کوتیشن نیز بدون شکستن markup نمایش داده شوند.
- live search نیز هنگام نمایش نتیجه‌ی خالی `aria-activedescendant` قبلی را پاک می‌کند تا screen reader روی گزینه‌ی حذف‌شده نماند؛ revision آن به `.3` bump شد.
- drawer مقایسه/علاقه‌مندی در revision plugin `1.4.0` focus را به کنترل آغازکننده برمی‌گرداند، با Tab داخل dialog نگه می‌دارد، با Escape/overlay بسته می‌شود و هنگام بازبودن اسکرول صفحه را قفل می‌کند؛ storage آن نیز در حالت مسدودبودن localStorage fail-safe است.
- ویجت پشتیبانی نیز در revision `1.4.0` همین الگوی حرفه‌ای را گرفت: focus trap، Escape، بازگرداندن focus، قفل اسکرول و ساخت امن لینک‌های ارتباطی بدون `innerHTML`.
- state دسترسی‌پذیر `aria-expanded` و `aria-controls` نیز برای launcher پشتیبانی و drawer مقایسه به‌صورت runtime تنظیم می‌شود تا markup موجود و screen reader هر دو state واقعی باز/بسته را ببینند.
- guard قدیمی دیگری که bottom navigation موبایل را فقط در صفحات `product-category` مخفی می‌کرد نیز خنثی شد؛ آخرین لایه‌ی premium اکنون نمایش، visibility، opacity و pointer-events آن را در عرض‌های زیر ۹۰۰ پیکسل تثبیت می‌کند.
- ترتیب stylesheet در HTML زنده‌ی archive نیز تأیید شد: `design-system.css` سپس `shop.css` و در نهایت `premium-polish.css`؛ بنابراین overrideهای نهایی واقعاً بعد از guardهای قدیمی اعمال می‌شوند.
- آخرین validation روی فایل‌های theme و plugin: همه‌ی JavaScriptها `node --check`، همه‌ی PHPها `php -l` و توازن CSS همگی بدون خطا (`JS_BAD=0`, `PHP_BAD=0`, `270/270`).
- آخرین تست checkout با session واقعی WooCommerce: افزودن محصول و صفحه پرداخت هر دو `HTTP 200`؛ داده‌ی checkout هنوز `payment_methods: []` برمی‌گرداند. بنابراین blocker فعلی پیکربندی درگاه است، نه قالب یا responsive UI، و فعال‌سازی درگاه بدون انتخاب صاحب فروشگاه انجام نشد.

## یافته‌ی performance

فایل CSS و اسکریپت‌های اصلی سبک باقی مانده‌اند (`premium-polish.css` حدود ۳۰KB و اسکریپت‌های تعامل حدود ۱۲KB مجموعاً). در اندازه‌گیری جدید، پاسخ PHP داخل Docker برای خانه حدود ۵.۰ ثانیه و صفحه محصول حدود ۴.۸ ثانیه بود؛ MariaDB سالم و PHP opcache فعال است، بنابراین این زمان همچنان بیشتر از اجرای WordPress روی bind-mount ویندوز/محیط Docker محلی می‌آید، نه از لایه‌ی بصری یا assetهای جدید.

## موارد خارج از ادعای ۱۰۰٪

برای ادعای نهایی ۱۰۰٪ هنوز باید checkout با داده‌ی واقعی سفارش، پرداخت، احراز هویت و صفحات admin در محیط staging/production با داده‌ی واقعی QA شوند. این موارد عمداً با داده‌ی آزمایشی ناقص دستکاری نشده‌اند.

## ماتریس تحویل و خودممیزی

| حوزه | وضعیت فعلی | شواهد | مورد باقی‌مانده |
|---|---|---|---|
| زبان بصری، رنگ، کارت، CTA و motion | انجام‌شده در محیط محلی | لایه‌ی نهایی premium و بازبینی routeهای اصلی | مقایسه‌ی نهایی با برند/محتوای production |
| responsive و mobile navigation | انجام‌شده در محیط محلی | خانه، فروشگاه، دسته، محصول، سبد، حساب و checkout | QA روی دستگاه‌ها و مرورگرهای واقعی |
| تعاملات و accessibility | انجام‌شده در کد و runtime | focus trap، Escape، ARIA، lightbox، drawer و live search | تست رسمی با screen reader |
| مسیر خرید WooCommerce | انجام‌شده تا checkout | session واقعی: add-to-cart و checkout هر دو HTTP 200 | ثبت سفارش واقعی و callback پرداخت |
| پرداخت | ناقص به‌دلیل محیط | checkout پاسخ `payment_methods: []` می‌دهد | انتخاب و پیکربندی درگاه توسط صاحب فروشگاه |
| performance | بهینه‌سازی‌شده در سطح قالب | preload، fetchpriority، lazy-load، aspect-ratio | benchmark production با cache/CDN واقعی |
