<?php
// File: DabestanSite/user/includes/auth_check.php
require_once '../config.php';
if (!isset($_SESSION['user_id']) || (isset($_SESSION['is_super_admin']) && $_SESSION['is_super_admin'])) {
    header("Location: login.php");
    exit;
}