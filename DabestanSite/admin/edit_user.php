<?php
// File: DabestanSite/admin/edit_user.php

require_once 'includes/auth_check.php';
$pdo = get_pdo_connection();

$page_title = "ویرایش کاربر";
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$message_type = '';

if ($user_id === 0) {
    header("Location: users.php");
    exit;
}

// Handle role assignment/removal
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Update user info
    if (isset($_POST['update_user'])) {
        $name = trim($_POST['name']);
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        if (empty($name) || empty($username)) {
            $message = "نام و نام کاربری نمی‌توانند خالی باشند.";
            $message_type = 'error';
        } else {
            try {
                if (!empty($password)) {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET name = ?, username = ?, password = ? WHERE id = ?");
                    $stmt->execute([$name, $username, $hashed_password, $user_id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE users SET name = ?, username = ? WHERE id = ?");
                    $stmt->execute([$name, $username, $user_id]);
                }
                $message = "اطلاعات کاربر با موفقیت به‌روزرسانی شد.";
                $message_type = 'success';
            } catch (PDOException $e) {
                $message = "خطا در به‌روزرسانی اطلاعات. ممکن است نام کاربری تکراری باشد.";
                $message_type = 'error';
            }
        }
    }

    // Add a new role
    if (isset($_POST['add_role'])) {
        $department_id = $_POST['department_id'];
        $role_id = $_POST['role_id'];
        try {
            $stmt = $pdo->prepare("INSERT INTO department_user_role (user_id, department_id, role_id) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $department_id, $role_id]);
            $message = "نقش جدید با موفقیت تخصیص داده شد.";
            $message_type = 'success';
        } catch (PDOException $e) {
            // Ignore errors for duplicate entries, which is fine
        }
    }

    // Remove a role
    if (isset($_POST['remove_role'])) {
        $department_id = $_POST['department_id'];
        $role_id = $_POST['role_id'];
        $stmt = $pdo->prepare("DELETE FROM department_user_role WHERE user_id = ? AND department_id = ? AND role_id = ?");
        $stmt->execute([$user_id, $department_id, $role_id]);
        $message = "نقش با موفقیت حذف شد.";
        $message_type = 'success';
    }
}

// Fetch user data
$stmt = $pdo->prepare("SELECT id, name, username FROM users WHERE id = ? AND is_super_admin = 0");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: users.php");
    exit;
}

// Fetch all departments and roles for forms
$departments = $pdo->query("SELECT id, name FROM departments ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$roles = $pdo->query("SELECT id, title FROM roles ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

// Fetch user's current roles
$stmt = $pdo->prepare("
    SELECT d.id as department_id, d.name as department_name, r.id as role_id, r.title as role_title
    FROM department_user_role dur
    JOIN departments d ON dur.department_id = d.id
    JOIN roles r ON dur.role_id = r.id
    WHERE dur.user_id = ?
");
$stmt->execute([$user_id]);
$user_roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                <h2>ویرایش کاربر: <?php echo htmlspecialchars($user['name']); ?></h2>
                <form action="edit_user.php?id=<?php echo $user_id; ?>" method="post">
                    <div class="form-group">
                        <label for="name">نام و نام خانوادگی:</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="username">نام کاربری:</label>
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="password">رمز عبور جدید:</label>
                        <input type="password" id="password" name="password">
                        <small style="color: #6c757d;">(برای عدم تغییر، این فیلد را خالی بگذارید)</small>
                    </div>
                    <button type="submit" name="update_user" class="submit-btn">به‌روزرسانی اطلاعات</button>
                    <a href="users.php" style="margin-right: 10px; text-decoration: none; color: #6c757d;">بازگشت به لیست</a>
                </form>
            </div>

            <div class="content-card">
                <h2>مدیریت نقش‌های کاربر</h2>
                
                <!-- Form to add a new role -->
                <form action="edit_user.php?id=<?php echo $user_id; ?>" method="post" class="form-container" style="border-bottom: 1px solid #eee; padding-bottom: 20px;">
                    <div class="form-group">
                        <label>تخصیص نقش جدید:</label>
                        <select name="department_id" required>
                            <option value="">-- انتخاب بخش --</option>
                            <?php foreach($departments as $dept): ?>
                                <option value="<?php echo $dept['id']; ?>"><?php echo htmlspecialchars($dept['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <select name="role_id" required>
                            <option value="">-- انتخاب نقش --</option>
                            <?php foreach($roles as $role): ?>
                                <option value="<?php echo $role['id']; ?>"><?php echo htmlspecialchars($role['title']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" name="add_role" class="submit-btn" style="background-color: #17a2b8;">تخصیص نقش</button>
                </form>

                <!-- Table of current roles -->
                <h3>نقش‌های فعلی:</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>بخش</th>
                            <th>نقش</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($user_roles)): ?>
                            <tr><td colspan="3" style="text-align:center;">این کاربر در حال حاضر هیچ نقشی ندارد.</td></tr>
                        <?php else: ?>
                            <?php foreach($user_roles as $ur): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($ur['department_name']); ?></td>
                                    <td><?php echo htmlspecialchars($ur['role_title']); ?></td>
                                    <td>
                                        <form action="edit_user.php?id=<?php echo $user_id; ?>" method="post" style="display:inline;">
                                            <input type="hidden" name="department_id" value="<?php echo $ur['department_id']; ?>">
                                            <input type="hidden" name="role_id" value="<?php echo $ur['role_id']; ?>">
                                            <button type="submit" name="remove_role" class="action-btn btn-delete">حذف نقش</button>
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