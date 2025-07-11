<?php
if (!defined('SITE_URL')) {
    // این حالت نباید رخ دهد اگر فایل به درستی از صفحات اصلی include شود
    die("دسترسی مستقیم به این فایل مجاز نیست.");
}
?>
        </main> <!-- بستن main.main-content -->

        <footer class="footer-main">
            <div class="container-fluid">
                <p style="text-align: center; padding: 15px 0; margin:0; font-size: 0.9rem; color: #6c757d;">
                    سامانه مدیریت دبستان &copy; <?php echo toPersianDate(date('Y-m-d H:i:s'), 'yyyy'); ?>
                </p>
            </div>
        </footer>

        <script src="<?php echo rtrim(SITE_URL, '/'); ?>/assets/js/script.js?v=<?php echo time(); // برای جلوگیری از کش شدن ?>"></script>
        <!-- در آینده می‌توانیم JS های مخصوص هر صفحه یا بخش را هم اضافه کنیم -->
        <script>
            // این تابع در script.js هم وجود دارد، اما برای اطمینان از اجرا، اینجا هم فراخوانی می‌کنیم
            // یا می‌توانیم مطمئن شویم script.js همیشه قبل از این اسکریپت‌های inline اجرا می‌شود.
            function initializeHeaderDateTime() {
                const dateElem = document.getElementById('persian-date-header');
                const timeElem = document.getElementById('persian-time-header');

                function updateDateTime() {
                    if (typeof toPersianDateForJS === 'function') {
                        const now = new Date();
                        if(dateElem) dateElem.textContent = toPersianDateForJS(now, 'yyyy/MM/dd');
                        if(timeElem) timeElem.textContent = toPersianDateForJS(now, 'HH:mm:ss');
                    } else {
                        // Fallback اگر toPersianDateForJS تعریف نشده باشد
                        const nowFallback = new Date();
                        if(dateElem) dateElem.textContent = nowFallback.toLocaleDateString('fa-IR', {timeZone: 'Asia/Tehran', year: 'numeric', month: '2-digit', day: '2-digit', numberingSystem: 'latn'});
                        if(timeElem) timeElem.textContent = nowFallback.toLocaleTimeString('fa-IR', {hour12: false, timeZone: 'Asia/Tehran', numberingSystem: 'latn'});
                    }
                }
                if (dateElem && timeElem) {
                    setInterval(updateDateTime, 1000);
                    updateDateTime(); // اجرای اولیه
                }
            }
            // اجرای تابع پس از بارگذاری کامل DOM یا به طور خاص پس از script.js
            if (document.readyState === "loading") {
                document.addEventListener("DOMContentLoaded", initializeHeaderDateTime);
            } else {
                initializeHeaderDateTime();
            }
        </script>
    </body>
    </html>
    <?php
    // بستن اتصال پایگاه داده (اگر در طول اسکریپت نمونه‌ای از کلاس Database ایجاد شده باشد)
    // این کار به طور خودکار توسط PHP در انتهای اسکریپت انجام می‌شود، اما برای اطمینان می‌توان فراخوانی کرد.
    // if (isset($db) && $db instanceof Database) {
    // $db->close();
    // }
    ?>
