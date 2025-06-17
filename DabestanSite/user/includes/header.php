<?php
// File: DabestanSite/user/includes/header.php (Master Layout Header)

// این دو خط باید در فایل اصلی صفحه (مثل داشبورد) باشند، نه اینجا.
// require_once 'includes/auth_check.php';
// require_once '../includes/jalali.php';
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'سامانه'; ?> - سامانه کاربری</title>
    <link rel="stylesheet" href="../assets/css/user_style.css">
    <link rel="stylesheet" href="../assets/css/admin_style.css"> <!-- برای استایل‌های مشترک مثل تگ وضعیت -->
</head>
<body>
    <div class="user-layout">
        
        <?php require_once 'sidebar.php'; ?>

        <!-- Main Content Wrapper -->
        <div class="main-content" id="main-content">
            <header class="top-header">
                <button class="menu-toggle" id="menu-toggle">☰</button>
                <div class="header-left">
                    <div class="datetime">
                        <span><?php echo jdate('l, j F Y'); ?></span>
                        <span id="live-time"><?php echo jdate('H:i:s'); ?></span>
                    </div>
                    <a href="logout.php" class="logout-btn">خروج</a>
                </div>
            </header>
            
            <main class="page-content">
                <!-- محتوای اصلی هر صفحه اینجا شروع می‌شود -->