<?php
// File: DabestanSite/admin/includes/header.php
require_once __DIR__ . '/../../includes/jalali.php';
?>
<header class="page-header">
    <div id="live-date-time"></div>
    <a href="logout.php" class="logout-btn">خروج</a>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateTimeElement = document.getElementById('live-date-time');
    
    function updateTime() {
        // We use PHP to get the accurate initial Jalali date and time
        const now = new Date();
        const serverTime = '<?php echo jdate("Y/m/d H:i:s"); ?>';
        const parts = serverTime.split(' ');
        const datePart = parts[0];
        const timePart = now.toLocaleTimeString('fa-IR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

        dateTimeElement.innerHTML = `<strong>امروز:</strong> ${datePart} | <strong>ساعت:</strong> ${timePart}`;
    }
    
    updateTime(); // Initial call
    setInterval(updateTime, 1000); // Update every second
});
</script>