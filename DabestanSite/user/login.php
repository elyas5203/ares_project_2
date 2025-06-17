<?php
// File: DabestanSite/user/login.php
require_once '../config.php';

if (isset($_SESSION['user_id'])) {
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
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
            $stmt = $pdo->prepare("SELECT id, name, password, is_super_admin FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && !$user['is_super_admin'] && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['is_super_admin'] = false;
                header("Location: dashboard.php");
                exit;
            } else {
                $error_message = "نام کاربری یا رمز عبور نامعتبر است.";
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
    <title>ورود به سامانه</title>
    <link rel="stylesheet" href="../assets/css/user_style.css">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-container">
            <h1>ورود به سامانه کاربری</h1>
            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            <form action="login.php" method="post">
                <input type="text" name="username" placeholder="نام کاربری" required>
                <input type="password" name="password" placeholder="رمز عبور" required>
                <button type="submit">ورود</button>
            </form>
        </div>
    </div>
</body>
</html>