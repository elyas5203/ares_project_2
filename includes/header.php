<?php
// این فایل باید همیشه بعد از config.php, functions.php فراخوانی شود
if (!defined('SITE_URL')) {
    // تلاش برای بارگذاری فایل‌های ضروری اگر مستقیما include نشده باشند
    // این حالت معمولا نباید رخ دهد اگر ساختار include صحیح باشد
    $base_path = __DIR__ . '/';
    if (file_exists($base_path . 'config.php')) require_once $base_path . 'config.php';
    else die("فایل config.php پیدا نشد.");

    if (file_exists($base_path . 'functions.php')) require_once $base_path . 'functions.php';
    else die("فایل functions.php پیدا نشد.");
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') . ' - ' : ''; ?>سامانه دبستان</title>
    <link rel="stylesheet" href="<?php echo rtrim(SITE_URL, '/'); ?>/assets/css/style.css?v=<?php echo time(); // برای جلوگیری از کش شدن ?>">
    <!-- در آینده می‌توانیم CSS های مخصوص هر صفحه یا بخش را هم اضافه کنیم -->
    <script>
        // تعریف SITE_URL برای استفاده در جاوااسکریپت اگر نیاز باشد
        const SITE_URL = '<?php echo rtrim(SITE_URL, '/'); ?>/';
    </script>
</head>
<body>
    <header class="header">
        <div class="logo-area">
            <button class="menu-toggle" id="menuToggleBtn" aria-label="Toggle Menu" title="منو">&#9776;</button>
            <h1 class="site-title"><?php echo isset($page_title) ? htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') : 'سامانه دبستان'; ?></h1>
        </div>
        <div class="datetime-container">
            <div id="persian-date-header">بارگذاری تاریخ...</div>
            <div id="persian-time-header">بارگذاری ساعت...</div>
        </div>
        <div class="user-info">
            <?php if(isLoggedIn()): ?>
                <span>سلام، <?php echo htmlspecialchars($_SESSION['full_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                <a href="<?php echo rtrim(SITE_URL, '/'); ?>/logout.php" class="btn btn-sm btn-danger" style="color:white; text-decoration:none; padding: 5px 10px; font-size:0.8rem; margin-right:10px;">خروج</a>
            <?php else: ?>
                 <a href="<?php echo rtrim(SITE_URL, '/'); ?>/login.php" class="btn btn-sm btn-primary" style="color:white; text-decoration:none; padding: 5px 10px; font-size:0.8rem;">ورود</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="sidebar" id="sidebar">
         <ul>
            <li><a href="<?php echo rtrim(SITE_URL, '/'); ?>/dashboard.php"><span class="icon">🏠</span> داشبورد</a></li>
            <!-- لینک‌های دیگر در آینده اضافه می‌شوند -->
            <?php if(isLoggedIn()): ?>
            <li><a href="<?php echo rtrim(SITE_URL, '/'); ?>/logout.php"><span class="icon">🚪</span> خروج</a></li>
            <?php endif; ?>
        </ul>
    </div>
    <div class="overlay" id="overlay"></div>

    <main class="main-content" id="mainContent">
        <!-- محتوای اصلی صفحه در اینجا توسط فایل فراخوانی کننده قرار می‌گیرد -->
        <div style="height: 60px;"></div> <!-- ایجاد فاصله به اندازه ارتفاع هدر ثابت -->

        <!-- نمایش پیام‌های عمومی که ممکن است در session باشند -->
        <?php
        flashMessage('auth_error', '', 'alert alert-danger');
        flashMessage('general_error', '', 'alert alert-danger');
        flashMessage('general_success', '', 'alert alert-success');
        ?>
