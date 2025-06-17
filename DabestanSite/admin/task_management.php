<?php
// File: DabestanSite/admin/task_management.php

require_once 'includes/auth_check.php';
require_once '../includes/jalali.php';

$page_title = "مدیریت وظایف";
$page_name = "tasks"; // for sidebar active state

$pdo = get_pdo_connection();
$success_message = '';
$error_message = '';

// --- Logic for Creating a New Task ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_task'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $department_id = filter_input(INPUT_POST, 'department_id', FILTER_VALIDATE_INT);

    if (empty($title) || !$department_id) {
        $error_message = "عنوان وظیفه و انتخاب بخش الزامی است.";
    } else {
        try {
            $sql = "INSERT INTO tasks (title, description, department_id, creator_id) VALUES (:title, :description, :department_id, :creator_id)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':title' => $title,
                ':description' => $description,
                ':department_id' => $department_id,
                ':creator_id' => $_SESSION['user_id']
            ]);
            $success_message = "وظیفه جدید با موفقیت ایجاد شد.";
        } catch (PDOException $e) {
            $error_message = "خطا در ایجاد وظیفه: " . $e->getMessage();
        }
    }
}

// --- Fetching Data for Display ---
// Fetch all tasks with department names
$tasks_stmt = $pdo->query("
    SELECT t.*, d.name AS department_name 
    FROM tasks t
    JOIN departments d ON t.department_id = d.id
    ORDER BY t.created_at DESC
");
$tasks = $tasks_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all departments for the creation form dropdown
$departments_stmt = $pdo->query("SELECT id, name FROM departments ORDER BY name");
$departments = $departments_stmt->fetchAll(PDO::FETCH_ASSOC);

// Helper array to translate status
$status_map = [
    'todo' => 'برای انجام',
    'in_progress' => 'در حال انجام',
    'done' => 'انجام شده'
];
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
            <header class="page-header">
                <h1><?php echo htmlspecialchars($page_title); ?></h1>
                <div class="header-left">
                    <div class="datetime">
                        <span><?php echo jdate('l, j F Y'); ?></span>
                        <span id="live-time"><?php echo jdate('H:i:s'); ?></span>
                    </div>
                    <a href="logout.php" class="logout-btn">خروج</a>
                </div>
            </header>

            <?php if ($success_message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>
            <?php if ($error_message): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <!-- Create New Task Form -->
            <div class="content-card">
                <h2>ایجاد وظیفه جدید</h2>
                <form action="task_management.php" method="post" class="management-form">
                    <div class="form-group">
                        <label for="title">عنوان وظیفه:</label>
                        <input type="text" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="description">توضیحات (اختیاری):</label>
                        <textarea id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="department_id">تخصیص به بخش:</label>
                        <select id="department_id" name="department_id" required>
                            <option value="">یک بخش را انتخاب کنید...</option>
                            <?php foreach ($departments as $department): ?>
                                <option value="<?php echo $department['id']; ?>"><?php echo htmlspecialchars($department['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="create_task">ایجاد وظیفه</button>
                    </div>
                </form>
            </div>

            <!-- List of All Tasks -->
            <div class="content-card">
                <h2>لیست تمام وظایف</h2>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>عنوان وظیفه</th>
                                <th>بخش مربوطه</th>
                                <th>وضعیت</th>
                                <th>تاریخ ایجاد</th>
                                <th>عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($tasks) > 0): ?>
                                <?php foreach ($tasks as $task): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($task['title']); ?></td>
                                        <td><?php echo htmlspecialchars($task['department_name']); ?></td>
                                        <td>
                                            <span class="task-status <?php echo $task['status']; ?>">
                                                <?php echo $status_map[$task['status']]; ?>
                                            </span>
                                        </td>
                                        <td><?php echo jdate('Y/m/d H:i', strtotime($task['created_at'])); ?></td>
                                        <td>
                                            <a href="#" class="action-btn view">مشاهده</a>
                                            <a href="#" class="action-btn edit">ویرایش</a>
                                            <a href="#" class="action-btn delete">حذف</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5">هیچ وظیفه‌ای تاکنون ایجاد نشده است.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    <script src="../assets/js/admin_script.js"></script>
</body>
</html>