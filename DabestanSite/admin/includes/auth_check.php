<?php
// File: DabestanSite/admin/includes/auth_check.php

// فایل کانفیگ را فراخوانی می‌کنیم تا به سشن دسترسی داشته باشیم
// مسیردهی باید از دیدگاه فایلی باشد که این فایل را include می‌کند (مثلا dashboard.php)
// پس مسیردهی از ریشه admin خواهد بود.
require_once '../config.php';

// بررسی می‌کنیم که آیا کاربر لاگین کرده و آیا واقعا ادمین اصلی است یا نه
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_super_admin']) || !$_SESSION['is_super_admin']) {
    // اگر لاگین نکرده بود یا ادمین نبود، او را به صفحه لاگین هدایت کن
    header("Location: login.php");
    exit; // اجرای اسکریپت را متوقف کن
}