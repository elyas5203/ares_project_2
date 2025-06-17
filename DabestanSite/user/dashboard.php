<?php
// File: DabestanSite/user/dashboard.php (Using the new layout)

require_once 'includes/auth_check.php';
require_once '../includes/jalali.php';

$page_title = "داشبورد";
$page_name = "dashboard";

// --- Page Specific Logic ---
$pdo = get_pdo_connection();
$user_id = $_SESSION['user_id'];
$sql = "SELECT d.name AS department_name, r.name AS role_name FROM department_user_role dur JOIN departments d ON dur.department_id = d.id JOIN roles r ON dur.role_id = r.id WHERE dur.user_id = :user_id ORDER BY d.name, r.id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$user_roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- Start of View ---
require_once 'includes/header.php';
?>

<div class="content-card">
    <h2>سلام، <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h2>
    <p>به سامانه سازمانی خوش آمدید. از طریق منوی کناری می‌توانید به بخش‌های مختلف دسترسی پیدا کنید.</p>
</div>

<div class="content-card roles-card">
    <h3>نقش‌ها و دسترسی‌های شما</h3>
    <?php if (count($user_roles) > 0): ?>
        <ul class="roles-list">
            <?php foreach ($user_roles as $role_info): ?>
                <li>
                    <span class="role-name"><?php echo htmlspecialchars($role_info['role_name']); ?></span>
                    در بخش
                    <strong class="department-name"><?php echo htmlspecialchars($role_info['department_name']); ?></strong>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="no-roles-message">در حال حاضر شما در هیچ بخشی عضو نیستید و نقشی برایتان تعریف نشده است.</p>
    <?php endif; ?>
</div>

<?php
// --- End of View ---
require_once 'includes/footer.php';
?>