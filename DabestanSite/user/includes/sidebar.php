<?php
// File: DabestanSite/user/includes/sidebar.php
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h3>سامانه کاربری</h3>
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="dashboard.php" class="<?php echo ($page_name == 'dashboard') ? 'active' : ''; ?>">
                <span>🏠</span> داشبورد
            </a>
        </li>
        <!-- آیتم جدید برای وظایف کاربر -->
        <li>
            <a href="tasks.php" class="<?php echo ($page_name == 'tasks') ? 'active' : ''; ?>">
                <span>📝</span> وظایف من
            </a>
        </li>
    </ul>
</aside>