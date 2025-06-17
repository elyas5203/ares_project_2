<?php
// File: DabestanSite/admin/login.php

require_once '../config.php';

// اگر کاربر به هر دلیلی به این صفحه آمده، سشن قبلی او را پاک می‌کنیم.
if (isset($_SESSION['user_id'])) {
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    session_start();
}

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error_message = "لطفاً نام کاربری و رمز عبور را وارد کنید.";
    } else {
        try {
            $pdo = get_pdo_connection();
            
            $stmt = $pdo->prepare("SELECT id, name, password, is_super_admin FROM users WHERE username = ? AND is_super_admin = 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['is_super_admin'] = true;

                header("Location: dashboard.php");
                exit;
            } else {
                $error_message = "دسترسی نامعتبر است. فقط مدیر اصلی می‌تواند وارد شود.";
            }
        } catch (PDOException $e) {
            $error_message = "خطایی در سیستم رخ داده است.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود مدیر اصلی</title>
    <link rel="stylesheet" href="../assets/css/admin_style.css">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-container">
            <h1>ورود به پنل مدیریت</h1>
            
            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <form action="login.php" method="post">
                <input type="text" name="username" placeholder="نام کاربری مدیر" required>
                <input type="password" name="password" placeholder="رمز عبور" required>
                <button type="submit">ورود</button>
            </form>
        </div>
    </div>
</body>
</html>