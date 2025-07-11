<?php
require_once 'includes/config.php'; // برای شروع session و دسترسی به SITE_URL
require_once 'includes/functions.php'; // برای تابع redirect و flashMessage

// تمامی متغیرهای session را پاک کن
$_SESSION = array();

// اگر از کوکی برای session استفاده می‌شود، آن را هم پاک کن
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// در نهایت session را از بین ببر
session_destroy();

// پیام موفقیت برای خروج (اختیاری)
// از آنجایی که بلافاصله ریدایرکت می‌شویم، این پیام باید در صفحه ورود نمایش داده شود.
// برای این کار از تابع flashMessage که قبلا ساختیم استفاده می‌کنیم.
flashMessage('logout_success', 'شما با موفقیت خارج شدید.', 'alert alert-info');

// کاربر را به صفحه ورود هدایت کن
redirect('login.php');
exit;
?>
