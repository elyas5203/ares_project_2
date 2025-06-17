<?php
// File: DabestanSite/user/tasks.php (Using the new layout)

require_once 'includes/auth_check.php';
require_once '../includes/jalali.php';

$page_title = "وظایف من";
$page_name = "tasks";

// --- Page Specific Logic ---
$pdo = get_pdo_connection();
$user_id = $_SESSION['user_id'];
$dept_sql = "SELECT department_id FROM department_user_role WHERE user_id = :user_id";
$dept_stmt = $pdo->prepare($dept_sql);
$dept_stmt->execute(['user_id' => $user_id]);
$department_ids = $dept_stmt->fetchAll(PDO::FETCH_COLUMN);
$tasks = [];
if (!empty($department_ids)) {
    $placeholders = implode(',', array_fill(0, count($department_ids), '?'));
    $tasks_sql = "SELECT t.*, d.name as department_name FROM tasks t JOIN departments d ON t.department_id = d.id WHERE t.department_id IN ($placeholders) ORDER BY t.created_at DESC";
    $tasks_stmt = $pdo->prepare($tasks_sql);
    $tasks_stmt->execute($department_ids);
    $tasks = $tasks_stmt->fetchAll(PDO::FETCH_ASSOC);
}
$status_map = ['todo' => 'برای انجام', 'in_progress' => 'در حال انجام', 'done' => 'انجام شده'];

// --- Start of View ---
require_once 'includes/header.php';
?>

<div class="content-card">
    <h2>لیست وظایف شما</h2>
    <p>در این بخش، تمام وظایفی که به بخش‌های تحت عضویت شما اختصاص داده شده است، نمایش داده می‌شود.</p>
    
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
                            <td data-label="عنوان وظیفه"><?php echo htmlspecialchars($task['title']); ?></td>
                            <td data-label="بخش مربوطه"><?php echo htmlspecialchars($task['department_name']); ?></td>
                            <td data-label="وضعیت">
                                <span class="task-status <?php echo $task['status']; ?>">
                                    <?php echo $status_map[$task['status']]; ?>
                                </span>
                            </td>
                            <td data-label="تاریخ ایجاد"><?php echo jdate('Y/m/d', strtotime($task['created_at'])); ?></td>
                            <td data-label="عملیات">
                                <a href="#" class="action-btn view">مشاهده جزئیات</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">در حال حاضر هیچ وظیفه‌ای برای شما تعریف نشده است.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// --- End of View ---
require_once 'includes/footer.php';
?>