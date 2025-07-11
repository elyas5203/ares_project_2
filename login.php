<?php
// فایل‌های ضروری را include می‌کنیم
// ابتدا config.php چون session_start() و تعاریف اولیه در آن است
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

// اگر کاربر از قبل وارد شده باشد، او را به داشبورد هدایت کن
if (isLoggedIn()) {
    if (isAdminPanelUser()) {
        redirect(ADMIN_URL . 'index.php'); // یا هر صفحه پیش‌فرض دیگر برای ادمین پنل
    } else {
        redirect('dashboard.php');
    }
}

$error_message = '';

// بررسی اینکه آیا فرم ارسال شده است
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password']; // sanitizeInput برای پسورد اعمال نمی‌شود چون با هش مقایسه می‌شود

    if (empty($username) || empty($password)) {
        $error_message = "نام کاربری و رمز عبور نمی‌توانند خالی باشند.";
    } else {
        try {
            $db = new Database();
            $db->query("SELECT id, username, password_hash, first_name, last_name, is_admin_panel_user FROM users WHERE username = :username AND status = 'active'");
            $db->bind(':username', $username);
            $user = $db->single();

            if ($user && verifyPassword($password, $user['password_hash'])) {
                // کاربر با موفقیت احراز هویت شد
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['is_admin_panel_user'] = (bool)$user['is_admin_panel_user'];

                // به‌روزرسانی آخرین زمان ورود (اختیاری)
                // $db->query("UPDATE users SET last_login_at = NOW() WHERE id = :id");
                // $db->bind(':id', $user['id']);
                // $db->execute();

                flashMessage('login_success', 'شما با موفقیت وارد شدید.', 'alert alert-success');

                if ($_SESSION['is_admin_panel_user']) {
                    // اگر کاربر، کاربر پنل ادمین اصلی بود به پنل ادمین هدایت شود
                    // فرض می‌کنیم صفحه اصلی پنل ادمین admin/index.php است
                    redirect(ADMIN_URL . 'index.php');
                } else {
                    // در غیر این صورت به داشبورد کاربران عادی هدایت شود
                    redirect('dashboard.php');
                }
            } else {
                $error_message = "نام کاربری یا رمز عبور نامعتبر است یا حساب شما غیرفعال می‌باشد.";
            }
        } catch (PDOException $e) {
            // در محیط عملیاتی، این خطا باید لاگ شود
            $error_message = "خطایی در ارتباط با پایگاه داده رخ داده است. لطفاً بعداً تلاش کنید.";
            // error_log("Login PDOException: " . $e->getMessage());
        } catch (Exception $e) {
            $error_message = "یک خطای غیرمنتظره رخ داده است.";
            // error_log("Login Exception: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود به سامانه دبستان</title>
    <link rel="stylesheet" href="assets/css/style.css"> <!-- فایل CSS عمومی -->
    <style>
        body {
            font-family: 'Tahoma', sans-serif;
            background-color: #f4f7f6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: #fff;
            padding: 30px 40px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-container h1 {
            color: #333;
            margin-bottom: 25px;
            font-size: 24px;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: right;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-size: 14px;
        }
        .form-group input[type="text"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        .form-group input[type="text"]:focus,
        .form-group input[type="password"]:focus {
            border-color: #007bff;
            outline: none;
        }
        .btn-login {
            background-color: #007bff;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background-color 0.3s;
        }
        .btn-login:hover {
            background-color: #0056b3;
        }
        .error-message {
            color: #dc3545;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .logo {
            margin-bottom: 20px;
            /* max-width: 150px; */ /* در صورت داشتن لوگو */
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- <img src="assets/images/logo.png" alt="لوگو" class="logo"> -->
        <h1>ورود به سامانه</h1>

        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <?php flashMessage('logout_success'); ?>
        <?php flashMessage('login_required', '', 'alert alert-warning'); ?>


        <form action="login.php" method="POST" novalidate>
            <div class="form-group">
                <label for="username">نام کاربری:</label>
                <input type="text" id="username" name="username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8') : ''; ?>">
            </div>
            <div class="form-group">
                <label for="password">رمز عبور:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" name="login" class="btn-login">ورود</button>
        </form>
        <!-- لینک فراموشی رمز عبور (در صورت نیاز بعدا اضافه می‌شود) -->
        <!-- <div style="margin-top: 15px;">
            <a href="forgot_password.php">فراموشی رمز عبور؟</a>
        </div> -->
    </div>
</body>
</html>
<?php
// پاک کردن خطاهای احتمالی از session که ممکن است از صفحات دیگر آمده باشند
if (isset($_SESSION['error_message'])) {
    unset($_SESSION['error_message']);
}
if (isset($_SESSION['success_message'])) {
    unset($_SESSION['success_message']);
}
?>
