<?php
// File: DabestanSite/admin/logout.php

require_once '../config.php';

// تمام متغیرهای سشن را پاک کن
$_SESSION = array();

// سشن را از بین ببر
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// کاربر را به صفحه لاگین هدایت کن
header("Location: login.php");
exit;