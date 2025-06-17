<?php
// File: DabestanSite/admin/edit_department.php

require_once 'includes/auth_check.php';
$pdo = get_pdo_connection();

$page_title = "ویرایش بخش";
$message = '';
$message_type = '';
$department_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($department_id === 0) {
    header("Location: departments.php");
    exit;
}

// Handle form submission for updating the department
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_department'])) {
    $name = trim($_POST['name']);
    $parent_id = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;

    if (empty($name)) {
        $message = "نام بخش نمی‌تواند خالی باشد.";
        $message_type = 'error';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE departments SET name = ?, parent_id = ? WHERE id = ?");
            $stmt->execute([$name, $parent_id, $department_id]);
            header("Location: departments.php?status=updated");
            exit;
        } catch (PDOException $e) {
            $message = "خطا در به‌روزرسانی بخش.";
            $message_type = 'error';
        }
    }
}

// Fetch the department's current data
try {
    $stmt = $pdo->prepare("SELECT * FROM departments WHERE id = ?");
    $stmt->execute([$department_id]);
    $department = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$department) {
        header("Location: departments.php");
        exit;
    }
} catch (PDOException $e) {
    die("خطا در دریافت اطلاعات بخش.");
}

// Fetch all other departments for the parent dropdown
$parent_departments = $pdo->query("SELECT id, name FROM departments WHERE id != $department_id ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

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
                <h2>ویرایش بخش: <?php echo htmlspecialchars($department['name']); ?></h2>
                <form action="edit_department.php?id=<?php echo $department_id; ?>" method="post">
                    <div class="form-group">
                        <label for="name">نام بخش:</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($department['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="parent_id">زیرمجموعه بخش:</label>
                        <select id="parent_id" name="parent_id">
                            <option value="">-- بخش اصلی (بدون والد) --</option>
                            <?php foreach ($parent_departments as $dept): ?>
                                <option value="<?php echo $dept['id']; ?>" <?php echo ($department['parent_id'] == $dept['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($dept['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" name="update_department" class="submit-btn">به‌روزرسانی</button>
                    <a href="departments.php" style="margin-right: 10px; text-decoration: none; color: #6c757d;">انصراف</a>
                </form>
            </div>
        </main>
    </div>
</body>
</html>