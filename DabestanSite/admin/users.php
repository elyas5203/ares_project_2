<?php
// File: DabestanSite/admin/users.php

require_once 'includes/auth_check.php';
require_once __DIR__ . '/../includes/jalali.php';
$pdo = get_pdo_connection();

$page_title = "مدیریت کاربران";
$message = '';
$message_type = '';

// Handle form submission for adding a new user
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($name) || empty($username) || empty($password)) {
        $message = "تمام فیلدها (نام، نام کاربری، رمز عبور) اجباری هستند.";
        $message_type = 'error';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $message = "این نام کاربری قبلا ثبت شده است.";
                $message_type = 'error';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (name, username, password) VALUES (?, ?, ?)");
                $stmt->execute([$name, $username, $hashed_password]);
                $message = "کاربر جدید با موفقیت اضافه شد.";
                $message_type = 'success';
            }
        } catch (PDOException $e) {
            $message = "خطا در افزودن کاربر.";
            $message_type = 'error';
        }
    }
}

// Handle user deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user'])) {
    $user_id_to_delete = $_POST['user_id'];
    if ($user_id_to_delete == $_SESSION['user_id']) {
        $message = "شما نمی‌توانید حساب کاربری خود را حذف کنید.";
        $message_type = 'error';
    } else {
        try {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND is_super_admin = 0");
            $stmt->execute([$user_id_to_delete]);
            $message = "کاربر با موفقیت حذف شد.";
            $message_type = 'success';
        } catch (PDOException $e) {
            $message = "خطا در حذف کاربر.";
            $message_type = 'error';
        }
    }
}


// Fetch all users except the super admin
$stmt = $pdo->prepare("SELECT id, name, username, created_at FROM users WHERE is_super_admin = 0 ORDER BY created_at DESC");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

            <?php if ($message): ?>
                <div class="message <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <div class="content-card form-container">
                <h2>افزودن کاربر جدید</h2>
                <form action="users.php" method="post">
                    <div class="form-group">
                        <label for="name">نام و نام خانوادگی:</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="username">نام کاربری:</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">رمز عبور:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" name="add_user" class="submit-btn">افزودن کاربر</button>
                </form>
            </div>

            <div class="content-card">
                <h2>لیست کاربران</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>نام</th>
                            <th>نام کاربری</th>
                            <th>تاریخ عضویت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="4" style="text-align: center;">هیچ کاربری یافت نشد.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo jdate('Y/m/d', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="action-btn btn-edit">ویرایش و نقش‌ها</a>
                                        <form action="users.php" method="post" style="display: inline;">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" name="delete_user" class="action-btn btn-delete" onclick="return confirm('آیا از حذف این کاربر مطمئن هستید؟ این عمل غیرقابل بازگشت است.');">حذف</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>