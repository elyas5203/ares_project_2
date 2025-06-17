<?php
// File: DabestanSite/admin/dashboard.php

require_once 'includes/auth_check.php';
$page_title = "داشبورد";
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - پنل مدیریت</title>
    <link rel="stylesheet" href="../assets/css/admin_style.css">
</head>
<body>
    <div class="admin-wrapper">
        
        <?php require_once 'includes/sidebar.php'; ?>

        <main class="main-content">
            <?php require_once 'includes/header.php'; ?>

            <div class="content-card">
                <p>
                    سلام <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>، به پنل مدیریت خوش آمدید.
                </p>
                <p>از این قسمت می‌توانید تمام بخش‌های سایت را مدیریت کنید.</p>
            </div>
        </main>
    </div>
</body>
</html>