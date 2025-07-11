<?php
// تعریف یک ثابت برای تشخیص اینکه در پنل ادمین هستیم
define('IN_ADMIN_PANEL', true);

require_once '../includes/config.php'; // مسیر به config.php یک سطح بالاتر است
require_once '../includes/database.php';
require_once '../includes/functions.php';

// بررسی اینکه آیا کاربر وارد شده و آیا کاربر پنل ادمین است
if (!isLoggedIn()) {
    flashMessage('login_required', 'برای دسترسی به این صفحه، لطفاً ابتدا وارد شوید.', 'alert alert-warning');
    redirect(SITE_URL . 'login.php'); // هدایت به صفحه ورود اصلی
}

if (!isAdminPanelUser()) {
    // اگر کاربر وارد شده ولی کاربر پنل ادمین نیست، او را به داشبورد کاربران عادی هدایت کن
    // یا یک پیام خطا نشان بده و اجازه دسترسی نده
    flashMessage('auth_error', 'شما اجازه دسترسی به پنل مدیریت را ندارید.', 'alert alert-danger');
    redirect(SITE_URL . 'dashboard.php');
}


// فایل‌های هدر و سایدبار مخصوص پنل ادمین (در مراحل بعد کامل‌تر می‌شوند)
$page_title = "پنل مدیریت اصلی";
// TODO: در آینده هدر و فوتر و سایدبار مخصوص ادمین ایجاد شود.
// require_once '../includes/admin_header.php';
// require_once '../includes/admin_sidebar.php';
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?> - سامانه دبستان</title>
    <!-- استفاده از استایل عمومی فعلا، بعدا می‌توان admin_style.css را کامل‌تر کرد -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/style.css">
    <?php if (file_exists(SITE_URL . 'assets/css/admin_style.css')): ?>
        <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/admin_style.css">
    <?php endif; ?>
    <style>
        /* استایل‌های موقت برای این صفحه ساده ادمین */
        body {
            font-family: Tahoma, sans-serif;
            margin: 0;
            background-color: #e9ecef; /* رنگ متفاوت برای ادمین */
            padding-top: 20px; /* فاصله از بالای صفحه */
        }
        .admin-main-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }
        .admin-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        .admin-card-header {
            padding: 15px 20px;
            background-color: #343a40; /* هدر تیره برای کارت ادمین */
            color: #fff;
            border-bottom: 1px solid #495057;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        .admin-card-header h1 {
            margin: 0;
            font-size: 1.5rem;
        }
        .admin-card-body {
            padding: 20px;
            color: #333;
        }
        .admin-card-body p {
            line-height: 1.7;
        }
        .admin-btn-logout {
            display: inline-block;
            background-color: #c82333; /* قرمز تیره‌تر */
            color: white !important;
            padding: 10px 18px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
            border: none;
            cursor: pointer;
        }
        .admin-btn-logout:hover {
            background-color: #bd2130;
        }
        .datetime-display-admin {
            font-weight: bold;
            color: #007bff; /* آبی برای تاریخ و زمان */
            margin: 10px 0;
        }
    </style>
</head>
<body class="admin-panel-body">
    <div class="admin-main-container">

        <div class="admin-card">
            <div class="admin-card-header">
                <h1><?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?></h1>
            </div>
            <div class="admin-card-body">
                <!-- نمایش پیام‌های فلش -->
                <?php flashMessage('login_success'); ?>

                <p>خوش آمدید، <strong><?php echo isset($_SESSION['full_name']) ? htmlspecialchars($_SESSION['full_name'], ENT_QUOTES, 'UTF-8') : 'ادمین'; ?></strong>!</p>
                <p>این پنل مدیریت اصلی سایت است. این بخش برای مدیریت کل سیستم استفاده خواهد شد و دامنه آن از پنل کاربران عادی جدا خواهد بود.</p>
                <div class="datetime-display-admin">
                    تاریخ و زمان فعلی (شمسی): <span id="current-persian-datetime-dynamic-admin"></span>
                </div>
                <p>
                    <a href="<?php echo SITE_URL; ?>logout.php" class="admin-btn-logout">خروج از حساب کاربری</a>
                </p>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card-header">
                <h2>لینک‌های مدیریتی (نمونه)</h2>
            </div>
            <div class="admin-card-body">
                <ul>
                    <li><a href="#">مدیریت کاربران</a> (هنوز پیاده‌سازی نشده)</li>
                    <li><a href="#">مدیریت نقش‌ها و دسترسی‌ها</a> (هنوز پیاده‌سازی نشده)</li>
                    <li><a href="#">تنظیمات فرم‌ها</a> (هنوز پیاده‌سازی نشده)</li>
                </ul>
            </div>
        </div>

    </div>

    <script>
        function updateAdminPageDateTime() {
            const elemAdmin = document.getElementById('current-persian-datetime-dynamic-admin');
            if (!elemAdmin) return;

            const now = new Date();
            let formattedDateTime;
            try {
                const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false, timeZone: 'Asia/Tehran', calendar: 'persian', numberingSystem: 'latn' };
                formattedDateTime = new Intl.DateTimeFormat('fa-IR', options).format(now);
            } catch (e) {
                formattedDateTime = now.toLocaleString('fa-IR', { timeZone: 'Asia/Tehran' }); // Fallback
            }
            elemAdmin.textContent = formattedDateTime;
        }

        if (document.getElementById('current-persian-datetime-dynamic-admin')) {
            setInterval(updateAdminPageDateTime, 1000);
            updateAdminPageDateTime(); // اجرای اولیه
        }
    </script>
    <!-- <?php // require_once '../includes/admin_footer.php'; ?> -->
</body>
</html>
