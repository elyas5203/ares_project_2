<?php
// File: DabestanSite/admin/departments.php

require_once 'includes/auth_check.php';
require_once __DIR__ . '/../includes/jalali.php';
$pdo = get_pdo_connection();

$page_title = "مدیریت بخش‌ها";
$message = '';
$message_type = '';

// Check for status messages from other pages (like edit)
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'updated') {
        $message = "بخش با موفقیت به‌روزرسانی شد.";
        $message_type = 'success';
    }
    if ($_GET['status'] == 'deleted') {
        $message = "بخش با موفقیت حذف شد.";
        $message_type = 'success';
    }
}

// Handle DELETE request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_department'])) {
    $department_id = $_POST['department_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM departments WHERE id = ?");
        $stmt->execute([$department_id]);
        header("Location: departments.php?status=deleted");
        exit;
    } catch (PDOException $e) {
        $message = "خطا در حذف بخش. ممکن است این بخش توسط سایر قسمت‌ها در حال استفاده باشد.";
        $message_type = 'error';
    }
}

// Handle ADD request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_department'])) {
    $name = trim($_POST['name']);
    $parent_id = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;

    if (empty($name)) {
        $message = "نام بخش نمی‌تواند خالی باشد.";
        $message_type = 'error';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO departments (name, parent_id) VALUES (?, ?)");
            $stmt->execute([$name, $parent_id]);
            $message = "بخش جدید با موفقیت اضافه شد.";
            $message_type = 'success';
        } catch (PDOException $e) {
            $message = "خطا در افزودن بخش.";
            $message_type = 'error';
        }
    }
}

// Fetch all departments for display
$stmt = $pdo->query("
    SELECT d1.id, d1.name, d1.created_at, d2.name AS parent_name 
    FROM departments d1 
    LEFT JOIN departments d2 ON d1.parent_id = d2.id 
    ORDER BY d1.created_at DESC
");
$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch departments for the dropdown
$parent_departments = $pdo->query("SELECT id, name FROM departments ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

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
                <h2>افزودن بخش جدید</h2>
                <form action="departments.php" method="post">
                    <div class="form-group">
                        <label for="name">نام بخش:</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="parent_id">زیرمجموعه بخش:</label>
                        <select id="parent_id" name="parent_id">
                            <option value="">-- بخش اصلی (بدون والد) --</option>
                            <?php foreach ($parent_departments as $dept): ?>
                                <option value="<?php echo $dept['id']; ?>"><?php echo htmlspecialchars($dept['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" name="add_department" class="submit-btn">افزودن</button>
                </form>
            </div>

            <div class="content-card">
                <h2>لیست بخش‌ها</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>نام بخش</th>
                            <th>بخش والد</th>
                            <th>تاریخ ایجاد</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($departments)): ?>
                            <tr>
                                <td colspan="4" style="text-align: center;">هیچ بخشی یافت نشد.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($departments as $department): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($department['name']); ?></td>
                                    <td><?php echo htmlspecialchars($department['parent_name'] ?? '---'); ?></td>
                                    <td><?php echo jdate('Y/m/d', strtotime($department['created_at'])); ?></td>
                                    <td>
                                        <a href="edit_department.php?id=<?php echo $department['id']; ?>" class="action-btn btn-edit">ویرایش</a>
                                        <form action="departments.php" method="post" style="display: inline;">
                                            <input type="hidden" name="department_id" value="<?php echo $department['id']; ?>">
                                            <button type="submit" name="delete_department" class="action-btn btn-delete" onclick="return confirm('آیا از حذف این بخش مطمئن هستید؟');">حذف</button>
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