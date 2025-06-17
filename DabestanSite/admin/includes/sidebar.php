<?php
// File: DabestanSite/admin/includes/sidebar.php
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <h3>پنل مدیریت</h3>
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="dashboard.php" class="<?php echo ($page_name == 'dashboard') ? 'active' : ''; ?>">
                داشبورد
            </a>
        </li>
        <li>
            <a href="department_management.php" class="<?php echo ($page_name == 'departments') ? 'active' : ''; ?>">
                مدیریت بخش‌ها
            </a>
        </li>
        <li>
            <a href="user_management.php" class="<?php echo ($page_name == 'users') ? 'active' : ''; ?>">
                مدیریت کاربران
            </a>
        </li>
        <!-- آیتم جدید برای وظایف -->
        <li>
            <a href="task_management.php" class="<?php echo ($page_name == 'tasks') ? 'active' : ''; ?>">
                مدیریت وظایف
            </a>
        </li>
    </ul>
</aside>