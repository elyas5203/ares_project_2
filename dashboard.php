<?php
require_once 'includes/config.php';
require_once 'includes/database.php'; // ممکن است در آینده برای نمایش اطلاعات نیاز باشد
require_once 'includes/functions.php';

// بررسی اینکه آیا کاربر وارد شده است
if (!isLoggedIn()) {
    flashMessage('login_required', 'برای دسترسی به این صفحه، لطفاً ابتدا وارد شوید.', 'alert alert-warning');
    redirect('login.php');
}

// اگر کاربر، کاربر پنل ادمین اصلی است، او را به پنل ادمین هدایت کن
// این حالت معمولا نباید رخ دهد اگر ریدایرکت در login.php درست کار کند
if (isAdminPanelUser()) {
    redirect(ADMIN_URL . 'index.php');
}

// فایل‌های هدر و سایدبار را include می‌کنیم (در مراحل بعد کامل‌تر می‌شوند)
// فعلا فایل‌های placeholder یا ساده ایجاد می‌کنیم
$page_title = "داشبورد کاربر"; // عنوان صفحه برای هدر
require_once 'includes/header.php'; // هدر عمومی برای پنل کاربران
// require_once 'includes/sidebar.php'; // سایدبار برای پنل کاربران (فعلا در header.php ادغام شده)
?>

<!-- محتوای اصلی از main در header.php شروع شده -->
<div class="container-fluid" style="padding-top: 20px;"> <!-- فاصله از هدر ثابت -->
    <!-- نمایش پیام‌های فلش -->
    <?php flashMessage('login_success'); ?>

    <div class="card">
        <div class="card-header">
            <?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?>
        </div>
        <div class="card-body">
            <h1>خوش آمدید، <?php echo isset($_SESSION['full_name']) ? htmlspecialchars($_SESSION['full_name'], ENT_QUOTES, 'UTF-8') : 'کاربر'; ?>!</h1>
            <p>این داشبورد کاربری شماست. امکانات به تدریج به این بخش اضافه خواهند شد.</p>
            <p>تاریخ و زمان فعلی (شمسی) در هدر نمایش داده می‌شود.</p>
            <p id="current-persian-datetime-dynamic-user-body" style="font-weight:bold;"></p> <!-- برای نمایش تاریخ در بدنه هم -->
            <p>
                <a href="logout.php" class="btn btn-danger">خروج از حساب کاربری</a>
            </p>
        </div>
    </div>

    <!-- سایر محتویات داشبورد در اینجا اضافه خواهند شد -->
    <div class="card">
        <div class="card-header">اطلاعات نمونه</div>
        <div class="card-body">
            <p>این یک کارت نمونه برای نمایش محتوای بیشتر است.</p>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>نام</th>
                            <th>نام خانوادگی</th>
                            <th>نقش</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>علی</td>
                            <td>رضایی</td>
                            <td>مدرس</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>زهرا</td>
                            <td>احمدی</td>
                            <td>عضو بخش پرورشی</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<!-- container-fluid -->

<script>
// تابع برای نمایش تاریخ و ساعت در بدنه صفحه کاربر
function updatePageUserDateTime() {
    const elemUserBody = document.getElementById('current-persian-datetime-dynamic-user-body');
    if (!elemUserBody) return;

    const now = new Date();
    let formattedDateTime;
    try {
        const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false, timeZone: 'Asia/Tehran', calendar: 'persian', numberingSystem: 'latn' };
        formattedDateTime = new Intl.DateTimeFormat('fa-IR', options).format(now);
    } catch (e) {
        formattedDateTime = now.toLocaleString('fa-IR', { timeZone: 'Asia/Tehran' });
    }
    elemUserBody.textContent = "ساعت و تاریخ فعلی در بدنه: " + formattedDateTime;
}

if (document.getElementById('current-persian-datetime-dynamic-user-body')) {
    setInterval(updatePageUserDateTime, 1000);
    updatePageUserDateTime();
}
</script>

<?php
// فایل فوتر را include می‌کنیم
require_once 'includes/footer.php';
?>
