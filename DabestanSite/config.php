<?php
// File: DabestanSite/config.php

// -- DATABASE SETTINGS --
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'dabestan_site');

// -- START SESSION --
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// -- DATABASE CONNECTION FUNCTION --
function get_pdo_connection() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("خطا در اتصال به پایگاه داده: " . $e->getMessage());
        }
    }
    return $pdo;
}