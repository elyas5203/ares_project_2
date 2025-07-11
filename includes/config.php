<?php
// تنظیمات پایگاه داده
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'dabestan_db');

// تنظیمات مربوط به آدرس سایت
define('SITE_URL', 'http://localhost/dabestan_site/');
define('ADMIN_URL', 'http://localhost/dabestan_site/admin/');

// تنظیمات منطقه زمانی
date_default_timezone_set('Asia/Tehran');

// فعال کردن نمایش خطاها
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// شروع session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
