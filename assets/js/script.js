document.addEventListener('DOMContentLoaded', function() {
    const menuToggleBtn = document.getElementById('menuToggleBtn');
    const sidebar = document.getElementById('sidebar');
    // const mainContent = document.getElementById('mainContent'); // دیگر برای shift استفاده نمی‌شود
    const overlay = document.getElementById('overlay');

    if (menuToggleBtn && sidebar) {
        menuToggleBtn.addEventListener('click', function(event) {
            event.stopPropagation(); // جلوگیری از بسته شدن بلافاصله توسط کلیک روی overlay
            sidebar.classList.toggle('active');
            if (overlay) {
                overlay.classList.toggle('active');
            }
        });
    }

    if (overlay) {
        overlay.addEventListener('click', function() {
            if (sidebar) sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
    }

    // بستن سایدبار با کلیک بیرون از آن (اگر روی overlay کلیک نشود)
    document.addEventListener('click', function(event) {
        if (sidebar && sidebar.classList.contains('active')) {
            const isClickInsideSidebar = sidebar.contains(event.target);
            const isClickOnToggleButton = menuToggleBtn ? menuToggleBtn.contains(event.target) : false;

            if (!isClickInsideSidebar && !isClickOnToggleButton) {
                sidebar.classList.remove('active');
                if (overlay) {
                    overlay.classList.remove('active');
                }
            }
        }
    });


    // توابع مربوط به تاریخ و زمان در هدر و بدنه صفحات
    // این توابع در فوتر هم فراخوانی می‌شوند تا از اجرای آن‌ها اطمینان حاصل شود.
    // تابع جاوااسکریپتی برای تبدیل تاریخ میلادی به شمسی (برای نمایش)
    // این تابع باید قبل از استفاده، تعریف شده باشد.
    window.toPersianDateForJS = function(dateObject, formatPattern) {
        if (!(dateObject instanceof Date)) {
            console.error("toPersianDateForJS: ورودی باید یک آبجکت Date باشد.");
            return "";
        }
        try {
            let options = {};
            // تبدیل فرمت PHP-like به فرمت Intl برای تاریخ
            // yyyy: سال چهار رقمی, yy: سال دو رقمی
            // MM: ماه دو رقمی, M: ماه یک رقمی
            // dd: روز دو رقمی, d: روز یک رقمی
            // EEEE: نام کامل روز هفته, EEE: نام کوتاه روز هفته
            // HH: ساعت ۲۴ ساعته, hh: ساعت ۱۲ ساعته
            // mm: دقیقه, ss: ثانیه
            // a: نشانگر AM/PM (اگر hh استفاده شود)

            if (formatPattern === 'yyyy/MM/dd') {
                options = { year: 'numeric', month: '2-digit', day: '2-digit', timeZone: 'Asia/Tehran', calendar: 'persian', numberingSystem: 'latn' };
            } else if (formatPattern === 'HH:mm:ss') {
                options = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false, timeZone: 'Asia/Tehran', calendar: 'persian', numberingSystem: 'latn' };
            } else if (formatPattern === 'yyyy/MM/dd HH:mm:ss') {
                 options = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false, timeZone: 'Asia/Tehran', calendar: 'persian', numberingSystem: 'latn' };
            }
             else { // فرمت پیش‌فرض یا کامل‌تر
                options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false, timeZone: 'Asia/Tehran', calendar: 'persian', numberingSystem: 'latn' };
            }

            let formattedDate = new Intl.DateTimeFormat('fa-IR', options).format(dateObject);

            // جایگزینی کاراکترهای جداکننده استاندارد فارسی با اسلش اگر فرمت yyyy/MM/dd باشد
            if (formatPattern === 'yyyy/MM/dd' || formatPattern === 'yyyy/MM/dd HH:mm:ss') {
                // Intl ممکن است از کاراکترهای فارسی برای جداکننده استفاده کند (٫)، آن‌ها را با / جایگزین می‌کنیم
                // مثال: ۱۴۰۳٫۰۳٫۱۲ -> ۱۴۰۳/۰۳/۱۲
                // این بخش ممکن است نیاز به تنظیم دقیق‌تری بر اساس خروجی Intl داشته باشد
                 if (formattedDate.includes('٫')) {
                    formattedDate = formattedDate.replace(/٬/g, '').replace(/٫/g, '/');
                 } else if (formattedDate.includes('.')) { // برای حالتی که اعداد انگلیسی هستند و جداکننده نقطه است
                    // اگر فرمت تاریخ شبیه 2024.06.01 بود
                    // این بخش نیاز به بررسی دقیق‌تر دارد چون Intl برای fa-IR معمولا نقطه نمی‌گذارد
                 }

                 // اطمینان از فرمت صحیح برای زمان در yyyy/MM/dd HH:mm:ss
                 if (formatPattern === 'yyyy/MM/dd HH:mm:ss') {
                    const parts = formattedDate.split(' ');
                    if (parts.length === 2 && parts[0].count('/') !== 2) { // اگر تاریخ و زمان با هم بودند ولی فرمت تاریخ بهم ریخته
                        const datePart = new Intl.DateTimeFormat('fa-IR', { year: 'numeric', month: '2-digit', day: '2-digit', timeZone: 'Asia/Tehran', calendar: 'persian', numberingSystem: 'latn' }).format(dateObject).replace(/٬/g, '').replace(/٫/g, '/');
                        const timePart = new Intl.DateTimeFormat('fa-IR', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false, timeZone: 'Asia/Tehran', calendar: 'persian', numberingSystem: 'latn' }).format(dateObject);
                        formattedDate = datePart + ' ' + timePart;
                    }
                 }
            }
            return formattedDate;

        } catch (e) {
            console.error("خطا در تبدیل تاریخ به شمسی (JS): ", e);
            // Fallback بسیار ساده
            if (formatPattern === 'HH:mm:ss') return dateObject.toLocaleTimeString('en-US', {hour12: false, timeZone: 'Asia/Tehran'}); //اعداد انگلیسی
            return dateObject.toLocaleDateString('en-US', {timeZone: 'Asia/Tehran'}); //اعداد انگلیسی
        }
    };

    // تابع برای به‌روزرسانی تاریخ و ساعت در هدر (فراخوانی از فوتر)
    // این تابع در اینجا تعریف شده و در فوتر فراخوانی می‌شود
    window.initializeHeaderDateTime = function() {
        const dateElem = document.getElementById('persian-date-header');
        const timeElem = document.getElementById('persian-time-header');

        function updateDateTimeInHeader() {
            const now = new Date();
            if(dateElem) dateElem.textContent = toPersianDateForJS(now, 'yyyy/MM/dd');
            if(timeElem) timeElem.textContent = toPersianDateForJS(now, 'HH:mm:ss');
        }

        if (dateElem && timeElem) {
            setInterval(updateDateTimeInHeader, 1000);
            updateDateTimeInHeader(); // اجرای اولیه
        }
    };

    // اجرای تابع برای هدر اگر این اسکریپت زودتر از اسکریپت داخل فوتر اجرا شود
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", window.initializeHeaderDateTime);
    } else {
        window.initializeHeaderDateTime();
    }

    // تابع برای به‌روزرسانی تاریخ و ساعت در بدنه صفحات (اگر المنت مربوطه وجود داشته باشد)
    function initializePageBodyDateTime() {
        const userBodyElem = document.getElementById('current-persian-datetime-dynamic-user-body');
        const adminBodyElem = document.getElementById('current-persian-datetime-dynamic-admin');

        function updateDateTimeInBody() {
            const now = new Date();
            const formattedFullDateTime = toPersianDateForJS(now, 'yyyy/MM/dd HH:mm:ss');
            if(userBodyElem) userBodyElem.textContent = "ساعت و تاریخ فعلی: " + formattedFullDateTime;
            if(adminBodyElem) adminBodyElem.textContent = formattedFullDateTime;
        }

        if (userBodyElem || adminBodyElem) {
            setInterval(updateDateTimeInBody, 1000);
            updateDateTimeInBody(); // اجرای اولیه
        }
    }
    initializePageBodyDateTime();

});
