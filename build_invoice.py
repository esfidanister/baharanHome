from docx import Document
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT, WD_CELL_VERTICAL_ALIGNMENT
from docx.enum.section import WD_SECTION
from docx.oxml import OxmlElement
from docx.oxml.ns import qn
from docx.enum.style import WD_STYLE_TYPE

OUT = r"C:\Project\CyberGIt\BaharanHome\فاکتور-و-شرح-خدمات-بهاران-هوم.docx"
NAVY = "17324D"; BLUE = "2F6B9A"; LIGHT = "EAF2F7"; GRID = "D9E1E7"; GOLD = "C99B45"; BLACK = "000000"

def shade(cell, fill):
    tcPr = cell._tc.get_or_add_tcPr(); shd = tcPr.find(qn('w:shd'))
    if shd is None: shd = OxmlElement('w:shd'); tcPr.append(shd)
    shd.set(qn('w:fill'), fill)

def borders(table, color=GRID):
    tblPr = table._tbl.tblPr; b = tblPr.first_child_found_in('w:tblBorders')
    if b is None: b = OxmlElement('w:tblBorders'); tblPr.append(b)
    for edge in ('top','left','bottom','right','insideH','insideV'):
        tag = 'w:' + edge; el = b.find(qn(tag))
        if el is None: el = OxmlElement(tag); b.append(el)
        el.set(qn('w:val'),'single'); el.set(qn('w:sz'),'6'); el.set(qn('w:space'),'0'); el.set(qn('w:color'),color)

def set_cell(cell, text, bold=False, color=BLACK, size=9.5, align=WD_ALIGN_PARAGRAPH.RIGHT):
    cell.text = ''
    p = cell.paragraphs[0]; p.alignment = align; p.paragraph_format.space_after = Pt(2)
    p.paragraph_format.line_spacing = 1.15
    r = p.add_run(str(text)); r.bold=bold; r.font.name='Tahoma'; r.font.size=Pt(size); r.font.color.rgb=RGBColor.from_string(color)
    cell.vertical_alignment = WD_CELL_VERTICAL_ALIGNMENT.CENTER
    tcPr = cell._tc.get_or_add_tcPr(); mar = tcPr.first_child_found_in('w:tcMar')
    if mar is None: mar=OxmlElement('w:tcMar'); tcPr.append(mar)
    for side in ('top','start','bottom','end'):
        e=mar.find(qn('w:'+side))
        if e is None: e=OxmlElement('w:'+side); mar.append(e)
        e.set(qn('w:w'),'100'); e.set(qn('w:type'),'dxa')

def table(headers, rows, widths=None, font=9.2):
    t=doc.add_table(rows=1, cols=len(headers)); t.alignment=WD_TABLE_ALIGNMENT.CENTER; t.autofit=False; borders(t)
    for i,h in enumerate(headers):
        set_cell(t.rows[0].cells[i],h,True,'FFFFFF',font,WD_ALIGN_PARAGRAPH.CENTER); shade(t.rows[0].cells[i],NAVY)
    for ri,row in enumerate(rows):
        cells=t.add_row().cells
        for i,v in enumerate(row):
            set_cell(cells[i],v,False,BLACK,font,WD_ALIGN_PARAGRAPH.RIGHT if i==1 else WD_ALIGN_PARAGRAPH.CENTER)
            if ri%2==1: shade(cells[i], 'F7FAFC')
    if widths:
        for row in t.rows:
            for i,w in enumerate(widths): row.cells[i].width=Inches(w)
    doc.add_paragraph().paragraph_format.space_after=Pt(2)
    return t

def heading(text, level=1):
    p=doc.add_paragraph(style=f'Heading {level}'); p.alignment=WD_ALIGN_PARAGRAPH.RIGHT; p.paragraph_format.space_before=Pt(10); p.paragraph_format.space_after=Pt(5)
    r=p.add_run(text); r.font.name='Tahoma'; r.font.color.rgb=RGBColor.from_string(BLACK); return p

def para(text, boldlead=None):
    p=doc.add_paragraph(); p.alignment=WD_ALIGN_PARAGRAPH.RIGHT; p.paragraph_format.space_after=Pt(5); p.paragraph_format.line_spacing=1.25
    if boldlead and text.startswith(boldlead):
        p.add_run(boldlead).bold=True; p.add_run(text[len(boldlead):])
    else: p.add_run(text)
    for r in p.runs: r.font.name='Tahoma'; r.font.size=Pt(10.5)
    return p

def bullets(items):
    for x in items:
        p=doc.add_paragraph(style='List Bullet'); p.alignment=WD_ALIGN_PARAGRAPH.RIGHT; p.paragraph_format.space_after=Pt(2); p.paragraph_format.line_spacing=1.15
        r=p.add_run(x); r.font.name='Tahoma'; r.font.size=Pt(10)

doc=Document(); sec=doc.sections[0]; sec.page_width=Inches(8.5); sec.page_height=Inches(11); sec.top_margin=Inches(.65); sec.bottom_margin=Inches(.65); sec.left_margin=Inches(.65); sec.right_margin=Inches(.65)
styles=doc.styles
for name in ['Normal','List Bullet']:
    styles[name].font.name='Tahoma'; styles[name]._element.rPr.rFonts.set(qn('w:eastAsia'),'Tahoma'); styles[name].font.size=Pt(10.5)
for n in [1,2,3]:
    s=styles[f'Heading {n}']; s.font.name='Tahoma'; s._element.rPr.rFonts.set(qn('w:eastAsia'),'Tahoma'); s.font.color.rgb=RGBColor.from_string(BLACK); s.font.bold=True; s.font.size=Pt(15 if n==1 else 12 if n==2 else 11)

# Header/footer
header=sec.header.paragraphs[0]; header.alignment=WD_ALIGN_PARAGRAPH.RIGHT; rr=header.add_run('بهاران هوم  |  پیش‌فاکتور طراحی و راه‌اندازی فروشگاه اینترنتی'); rr.font.name='Tahoma'; rr.font.size=Pt(8); rr.font.color.rgb=RGBColor.from_string(BLUE)
footer=sec.footer.paragraphs[0]; footer.alignment=WD_ALIGN_PARAGRAPH.CENTER; rr=footer.add_run('بهاران هوم  •  تاریخ صدور: ۱۴۰۵/۰۹/۰۳  •  مبالغ به تومان مگر آنکه خلاف آن ذکر شود'); rr.font.name='Tahoma'; rr.font.size=Pt(8); rr.font.color.rgb=RGBColor.from_string('666666')

p=doc.add_paragraph(style='Title'); p.alignment=WD_ALIGN_PARAGRAPH.RIGHT; r=p.add_run('فاکتور و شرح خدمات بهاران هوم'); r.font.name='Tahoma'; r.font.size=Pt(23); r.font.bold=True; r.font.color.rgb=RGBColor.from_string(BLACK)
p=doc.add_paragraph(); p.alignment=WD_ALIGN_PARAGRAPH.RIGHT; r=p.add_run('طراحی، پیاده‌سازی و پشتیبانی فروشگاه اینترنتی وردپرس و ووکامرس'); r.font.name='Tahoma'; r.font.size=Pt(12); r.font.color.rgb=RGBColor.from_string(BLUE)
para('این پیش‌فاکتور، ارزش واقعی خدمات انجام‌شده، تخفیف ویژه همکاری و هزینه‌های قابل پرداخت را به‌صورت شفاف نشان می‌دهد. خدمات پشتیبانی، هوش مصنوعی پیشرفته و SEO نیز در بخش جداگانه ارائه شده‌اند و فقط در صورت سفارش فعال می‌شوند.')

heading('خلاصه مالی پروژه')
table(['شرح','مبلغ پایه','تخفیف همکاری','مبلغ نهایی'],[
    ['طراحی و راه‌اندازی فروشگاه اینترنتی بهاران هوم','۱۰۳٬۰۰۰٬۰۰۰','۸۰٬۰۰۰٬۰۰۰','۲۳٬۰۰۰٬۰۰۰'],
    ['جمع قابل پرداخت بابت پروژه','۱۰۳٬۰۰۰٬۰۰۰','۸۰٬۰۰۰٬۰۰۰','۲۳٬۰۰۰٬۰۰۰'],
], [2.6,1.45,1.45,1.45], 9.2)
para('مبلغ قابل پرداخت بابت هاست، دامنه و اتصال اولیه API: ۲۳٬۰۰۰٬۰۰۰ تومان')
para('ارزش خدمات طراحی، توسعه و راه‌اندازی: ۸۰٬۰۰۰٬۰۰۰ تومان که به‌طور کامل به‌عنوان تخفیف همکاری رایگان محاسبه شده است.')

heading('فاکتور تفصیلی اجرای پروژه')
rows=[
['۱','هاست و دامنه و راه‌اندازی اولیه','۱۶٬۰۰۰٬۰۰۰','۰','۱۶٬۰۰۰٬۰۰۰'],
['۲','اتصال API.ir و راه‌اندازی اولیه چت‌بات','۷٬۰۰۰٬۰۰۰','۰','۷٬۰۰۰٬۰۰۰'],
['۳','تحلیل نیازمندی، ساختار فروشگاه و معماری صفحات','۵٬۰۰۰٬۰۰۰','۵٬۰۰۰٬۰۰۰','رایگان'],
['۴','طراحی سیستم بصری، رنگ‌بندی، تایپوگرافی و RTL','۸٬۰۰۰٬۰۰۰','۸٬۰۰۰٬۰۰۰','رایگان'],
['۵','پیاده‌سازی قالب اختصاصی وردپرس و ووکامرس','۲۰٬۰۰۰٬۰۰۰','۲۰٬۰۰۰٬۰۰۰','رایگان'],
['۶','طراحی صفحه اصلی و سکشن‌های اختصاصی','۱۰٬۰۰۰٬۰۰۰','۱۰٬۰۰۰٬۰۰۰','رایگان'],
['۷','فروشگاه، دسته‌بندی، جستجو و فیلتر محصولات','۷٬۰۰۰٬۰۰۰','۷٬۰۰۰٬۰۰۰','رایگان'],
['۸','صفحه محصول، گالری، تنوع، سبد و مسیر خرید','۹٬۰۰۰٬۰۰۰','۹٬۰۰۰٬۰۰۰','رایگان'],
['۹','افزونه اختصاصی فروشگاه، CRM سبک و چت پشتیبانی','۱۰٬۰۰۰٬۰۰۰','۱۰٬۰۰۰٬۰۰۰','رایگان'],
['۱۰','ورود و عضویت اختصاصی و پنل مدیریت','۵٬۰۰۰٬۰۰۰','۵٬۰۰۰٬۰۰۰','رایگان'],
['۱۱','کاتالوگ، ورود محصولات و تنظیم دسته‌بندی‌ها','۳٬۰۰۰٬۰۰۰','۳٬۰۰۰٬۰۰۰','رایگان'],
['۱۲','بهینه‌سازی موبایل، سرعت، دسترس‌پذیری و تست','۳٬۰۰۰٬۰۰۰','۳٬۰۰۰٬۰۰۰','رایگان'],
]
table(['ردیف','شرح خدمت','قیمت پایه','تخفیف','پس از تخفیف'],rows,[.48,2.7,1.2,1.1,1.25],8.4)
para('جمع ارزش خدمات: ۱۰۳٬۰۰۰٬۰۰۰ تومان  |  تخفیف همکاری: ۸۰٬۰۰۰٬۰۰۰ تومان  |  مبلغ قابل پرداخت: ۲۳٬۰۰۰٬۰۰۰ تومان')

heading('شرح دقیق خروجی‌های اجراشده')
heading('قالب و سبک طراحی',2)
bullets(['قالب اختصاصی فروشگاهی RTL و فارسی، توسعه‌یافته برای وردپرس و WooCommerce؛','سبک بصری مدرن، مینیمال، Editorial و E-commerce با چیدمان Bento؛','رنگ‌بندی سازمانی با زمینه روشن، رنگ اصلی آبی/سبز و رنگ تأکیدی طلایی؛','طراحی واکنش‌گرا برای دسکتاپ، تبلت و موبایل با نقاط شکست ویژه ۳۶۰ و ۳۹۰ پیکسل؛','استفاده از CSS و JavaScript سبک، بدون وابستگی به صفحه‌ساز سنگین یا کتابخانه اسلایدر خارجی؛','پشتیبانی از RTL، Focus قابل مشاهده، Skip Link، Keyboard Navigation و Reduced Motion.'])
heading('صفحات و اجزای طراحی‌شده',2)
bullets(['صفحه اصلی با هدر چندلایه، Utility Bar، Mega Menu، جستجو، حساب کاربری و سبد خرید؛','Hero اختصاصی با محصول واقعی ووکامرس و دکمه‌های CTA؛','دسته‌بندی‌ها، محصولات منتخب، پرفروش‌ها، پیشنهادهای ویژه، برندها و بنر کمپین؛','سکشن راهنمای انتخاب، مجله، مراحل خرید، خدمات و فوتر؛','صفحه فروشگاه، آرشیو دسته‌بندی، جستجو، مرتب‌سازی و فیلتر قیمت، برند، دسته و موجودی؛','حالت Grid/List، پنل فیلتر موبایل، Empty State و Breadcrumb؛','صفحه محصول با گالری، Thumbnail، زوم، Lightbox، تنوع، مشخصات، FAQ، دیدگاه و نوار خرید چسبان موبایل؛','سبد خرید، تسویه‌حساب، حساب کاربری، ورود و عضویت و صفحات مقاله/مجله.'])
heading('افزونه‌ها و قابلیت‌های اختصاصی',2)
table(['بخش','قابلیت‌های تحویل‌شده'],[
['WooCommerce','محصول، دسته، برند، ویژگی، موجودی، قیمت، تخفیف، سفارش، سبد، تسویه‌حساب و حساب کاربری'],
['Baharaan Commerce Toolkit','تنظیمات اختصاصی، کانال‌های تماس، راهنمای خرید، CRM سبک، چت، تنظیم Hero و سکشن‌ها، WebP و Import/Export'],
['ورود و عضویت بهاران','ساختار ورود، ثبت‌نام، بازیابی و آماده‌سازی احراز هویت اختصاصی'],
['داشبورد مدیریت','KPI فروش، سفارش‌های اخیر، محصولات کم‌موجودی، پرفروش‌ها، نمودار و بازه‌های زمانی'],
['سئو و عملکرد','Canonical، Open Graph، Schema هماهنگ با SEO، Lazy Loading، srcset، Preload تصویر اصلی و کاهش CLS'],
], [1.7,4.9], 9)

heading('خدمات پشتیبانی و سرویس‌های تکمیلی')
para('خدمات زیر پس از تحویل پروژه، به‌صورت انتخابی و با قرارداد جداگانه ارائه می‌شوند. فعال‌سازی هر خدمت منوط به انتخاب پکیج و پرداخت دوره مربوط است.')
table(['خدمت جداگانه','دوره','مبلغ','شامل'],[
['پشتیبانی پایه','ماهانه','۷٬۰۰۰٬۰۰۰ تومان','رفع خطا، به‌روزرسانی، بررسی امنیت، کنترل بکاپ، پایش فروشگاه و اصلاحات سبک'],
['پشتیبانی پایه','شش‌ماهه','۳۹٬۰۰۰٬۰۰۰ تومان','شش ماه پشتیبانی با تخفیف پرداخت یکجا'],
['پشتیبانی پایه','سالانه','۷۰٬۰۰۰٬۰۰۰ تومان','دوازده ماه پشتیبانی با تخفیف پرداخت یکجا'],
['چت‌بات حرفه‌ای و هوش مصنوعی','ماهانه','۲۵ دلار','OpenRouter یا سرویس سازگار با ChatGPT، پاسخ‌گویی محصولات، تولید مقاله و پاسخ‌گویی عمومی'],
], [1.55,1.05,1.25,2.75], 8.8)
para('پشتیبانی شامل توسعه قابلیت جدید، تولید محتوای مستمر، هزینه سرویس‌های خارجی، پیامک، درگاه یا لایسنس افزونه‌های تجاری نیست و این موارد در صورت نیاز جداگانه اعلام می‌شوند.')

heading('پکیج‌های SEO و معرفی در گوگل و سرویس‌های هوش مصنوعی')
para('هدف این خدمات، افزایش دیده‌شدن برند و محصولات در Google، موتورهای جست‌وجوی هوش مصنوعی و وب‌سایت‌های معتبر مرتبط است. اجرای SEO نیازمند استمرار است؛ بنابراین شروع کار با پرداخت دوره‌ای انجام می‌شود و نتیجه به رقابت بازار، کیفیت محتوا، اعتبار دامنه و بودجه انتشار وابسته است.')
table(['پکیج','ماهانه','شش‌ماهه','سالانه','مناسب برای'],[
['شروع','۳۵ دلار','۱۹۰ دلار','۳۶۰ دلار','راه‌اندازی اصولی SEO، اصلاح ساختار فنی، Search Console، نقشه سایت، عنوان و متا، Schema و گزارش ماهانه'],
['رشد','۷۵ دلار','۴۱۰ دلار','۷۸۰ دلار','تمام خدمات شروع، تولید محتوای هدفمند، بهینه‌سازی صفحات محصول، لینک‌سازی داخلی، پایش رقبا و معرفی محدود'],
['حرفه‌ای','۱۵۰ دلار','۸۲۰ دلار','۱۵۶۰ دلار','تمام خدمات رشد، تقویم محتوایی، Digital PR، رپورتاژ هدفمند، معرفی به سایت‌های مرتبط و بهینه‌سازی برای پاسخ‌های AI'],
], [1.0,0.85,0.85,0.85,3.0], 8.2)
para('پرداخت ماهانه برای شروع و ارزیابی مناسب است. پرداخت شش‌ماهه و سالانه با تخفیف دوره‌ای انجام می‌شود. هزینه رپورتاژهای پولی، بک‌لینک معتبر، ترجمه، تصویرسازی، تبلیغات، سرویس‌های API و انتشار در رسانه‌های گران‌قیمت جداگانه و پس از تأیید مشتری محاسبه خواهد شد.')

heading('ارزیابی پروژه از نگاه ده متخصص',2)
table(['حوزه تخصصی','جمع‌بندی کارشناسی'],[
['مدیر پروژه','خروجی پروژه یک فروشگاه قابل توسعه با مرزبندی روشن بین هسته فروش، قالب و افزونه اختصاصی است.'],
['طراح UI/UX','هویت بصری یکپارچه، مسیر خرید مشخص و طراحی جداگانه برای موبایل اجرا شده است.'],
['توسعه‌دهنده WordPress','قالب و افزونه‌ها بدون ویرایش هسته وردپرس و WooCommerce ساخته شده‌اند.'],
['متخصص WooCommerce','قیمت، موجودی، سفارش، تنوع محصول و checkout از داده‌های واقعی ووکامرس استفاده می‌کنند.'],
['متخصص Frontend','تعاملات با CSS و JavaScript سبک، RTL، Grid/List، فیلتر و Lightbox پیاده‌سازی شده‌اند.'],
['متخصص SEO','ساختار canonical، متادیتا، Open Graph، Schema و تصاویر responsive در نظر گرفته شده است.'],
['متخصص Performance','Preload، Lazy Loading، srcset و کنترل CLS انجام شده؛ اندازه‌گیری نهایی روی هاست واقعی لازم است.'],
['متخصص Accessibility','Focus، Escape، Skip Link، Keyboard Navigation و Reduced Motion لحاظ شده است.'],
['متخصص امنیت','کلید API سمت سرور، nonce، capability و محدودیت درخواست برای بخش‌های حساس استفاده شده است.'],
['کارشناس پشتیبانی','پشتیبانی، سرویس‌های بیرونی، SEO و توسعه‌های آتی باید جدا از تحویل اولیه قیمت‌گذاری شوند.'],
], [1.45,5.15], 8.6)

heading('شرایط مالی و تحویل')
bullets(['مبلغ قابل پرداخت فاکتور: ۲۳٬۰۰۰٬۰۰۰ تومان؛','ارزش خدمات طراحی، توسعه و راه‌اندازی به مبلغ ۸۰٬۰۰۰٬۰۰۰ تومان به‌عنوان تخفیف همکاری رایگان محاسبه شده است؛','هزینه پشتیبانی، هوش مصنوعی حرفه‌ای و SEO اختیاری و جدا از فاکتور اصلی است؛','هزینه سرویس‌های خارجی مانند OpenRouter، پیامک، درگاه، رپورتاژ و لایسنس‌ها در صورت مصرف جداگانه است؛','محتوای متنی، تصاویر نهایی، اطلاعات تماس، قوانین فروشگاه و اطلاعات واقعی محصولات با تأیید مشتری نهایی می‌شود؛','فعال‌سازی چت‌بات حرفه‌ای و SEO پس از انتخاب پکیج و پرداخت دوره مربوط انجام می‌شود؛','اعتبار این پیش‌فاکتور ۱۵ روز از تاریخ صدور است.'])
para('نام و امضای مجری: ....................................................        نام و امضای مشتری: ....................................................')

doc.core_properties.title='فاکتور و شرح خدمات بهاران هوم'; doc.core_properties.subject='پیش فاکتور طراحی و پشتیبانی فروشگاه اینترنتی'; doc.core_properties.author='Baharan Home'
doc.save(OUT)
print('done')
