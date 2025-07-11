<?php
if (!defined('SITE_URL')) {
    if (file_exists('config.php')) {
        require_once 'config.php';
    } elseif (file_exists('../includes/config.php')) {
        require_once '../includes/config.php';
    } else {
        // اگر در محیط تست یا CLI هستیم و config.php در مسیر استاندارد نیست،
        // سعی می‌کنیم از یک مسیر پیش‌فرض دیگر بارگذاری کنیم.
        // این بخش ممکن است نیاز به تنظیم دقیق‌تری داشته باشد.
        $config_path = __DIR__ . '/config.php';
        if (file_exists($config_path)) {
             require_once $config_path;
        } else {
            // Fallback نهایی اگر فایل config در هیچ مسیری پیدا نشود
            // این حالت نباید در اجرای عادی وب سرور رخ دهد اگر ساختار فایل صحیح باشد
            if (defined('STDIN')) { // بررسی اینکه آیا از طریق CLI اجرا می‌شود
                 //尝试从 یک سطح بالاتر لود شود، شاید functions.php در یک زیر پوشه باشد
                $config_path_cli = dirname(__DIR__) . '/includes/config.php';
                 if (file_exists($config_path_cli)) {
                    require_once $config_path_cli;
                 } else {
                    die("فایل config.php پیدا نشد. مسیر فعلی: " . __DIR__ . " و مسیر تلاش شده: " . $config_path_cli);
                 }
            } else {
                die("فایل config.php پیدا نشد. مسیر فعلی: " . __DIR__);
            }
        }
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdminPanelUser() {
    return isset($_SESSION['is_admin_panel_user']) && $_SESSION['is_admin_panel_user'] === true;
}

function redirect($url) {
    // اطمینان از اینکه $url با SITE_URL یا ADMIN_URL شروع نشده باشد تا دوباره اضافه نشود
    if (strpos($url, SITE_URL) !== 0 && strpos($url, ADMIN_URL) !== 0 && !preg_match('/^https?:\/\//', $url)) {
        // اگر آدرس نسبی است و برای پنل ادمین نیست، از SITE_URL استفاده کن
        // این بخش نیاز به منطق دقیق‌تری برای تشخیص پنل ادمین دارد اگر URL ها پیچیده‌تر شوند
        $base = SITE_URL; // پیش فرض برای پنل کاربران
        // یک راه ساده برای تشخیص اینکه آیا در حال حاضر در پنل ادمین هستیم
        if (defined('IN_ADMIN_PANEL') && IN_ADMIN_PANEL === true) {
            $base = ADMIN_URL;
        }
        $url = rtrim($base, '/') . '/' . ltrim($url, '/');
    }
    header("Location: " . $url);
    exit;
}

function flashMessage($name = '', $message = '', $class = 'alert alert-success') {
    if (!empty($name)) {
        if (!empty($message) && empty($_SESSION[$name])) {
            if (!empty($_SESSION[$name])) {
                unset($_SESSION[$name]);
            }
            if (!empty($_SESSION[$name . '_class'])) {
                unset($_SESSION[$name . '_class']);
            }
            $_SESSION[$name] = $message;
            $_SESSION[$name . '_class'] = $class;
        } elseif (empty($message) && !empty($_SESSION[$name])) {
            $class_to_use = !empty($_SESSION[$name . '_class']) ? $_SESSION[$name . '_class'] : $class;
            echo '<div class="' . htmlspecialchars($class_to_use, ENT_QUOTES, 'UTF-8') . '" id="msg-flash">' . htmlspecialchars($_SESSION[$name], ENT_QUOTES, 'UTF-8') . '</div>';
            unset($_SESSION[$name]);
            unset($_SESSION[$name . '_class']);
        }
    }
}

function toPersianDate($gregorian_date, $format = 'yyyy/MM/dd', $include_time = false) {
    if (empty($gregorian_date) || $gregorian_date == '0000-00-00 00:00:00' || $gregorian_date == '0000-00-00') {
        return '';
    }
    try {
        $date_time = new DateTime($gregorian_date);

        $pattern = $format; // IntlDateFormatter از فرمت ICU استفاده می‌کند
        // تبدیل فرمت PHP به فرمت ICU (ساده شده)
        $pattern = str_replace('Y', 'yyyy', $pattern);
        $pattern = str_replace('m', 'MM', $pattern);
        $pattern = str_replace('d', 'dd', $pattern);

        if ($include_time) {
            $time_pattern_exists = strpos($pattern, 'H') !== false || strpos($pattern, 'h') !== false ||
                                 strpos($pattern, 'm') !== false || // دقیقه
                                 strpos($pattern, 's') !== false;
            if (!$time_pattern_exists) {
                 $pattern .= ' HH:mm:ss'; // اضافه کردن فرمت زمان اگر در فرمت اصلی نباشد
            }
        }
        $pattern = str_replace('i', 'mm', $pattern); // دقیقه
        $pattern = str_replace('s', 'ss', $pattern); // ثانیه


        if (class_exists('IntlDateFormatter')) {
            $formatter = new IntlDateFormatter(
                'fa_IR@calendar=persian', // برای اعداد فارسی در تاریخ: fa@numbers=latn اگر اعداد انگلیسی می‌خواهید
                IntlDateFormatter::FULL, // date type - نادیده گرفته می‌شود وقتی پترن مشخص است
                IntlDateFormatter::FULL, // time type - نادیده گرفته می‌شود وقتی پترن مشخص است
                'Asia/Tehran',
                IntlDateFormatter::TRADITIONAL, // یا IntlDateFormatter::GREGORIAN برای اعداد لاتین
                $pattern
            );
            if ($formatter) {
                $timestamp = $date_time->getTimestamp();
                $formatted_date = $formatter->format($timestamp);
                if ($formatted_date === false) {
                    // echo "Error in IntlDateFormatter: " . $formatter->getErrorMessage(); // برای دیباگ
                    // Fallback به فرمت ساده اگر تبدیل با پترن پیچیده ناموفق بود
                    $formatter_fallback = new IntlDateFormatter('fa_IR@calendar=persian', IntlDateFormatter::SHORT, IntlDateFormatter::NONE, 'Asia/Tehran', IntlDateFormatter::TRADITIONAL);
                    return $formatter_fallback->format($timestamp) . ($include_time ? $date_time->format(' H:i:s') : '');
                }
                return $formatted_date;
            }
        }
        // Fallback اگر IntlDateFormatter در دسترس نباشد یا خطا دهد
        $php_time_format = $include_time ? ' H:i:s' : '';
        return "[INTL_REQ] " . $date_time->format('Y-m-d' . $php_time_format);

    } catch (Exception $e) {
        // echo $e->getMessage(); // For debugging
        return 'تاریخ نامعتبر';
    }
}

function getCurrentPersianDateTime($format = 'yyyy/MM/dd HH:mm:ss') {
    return toPersianDate(date('Y-m-d H:i:s'), $format, true);
}

function getCurrentPersianDate($format = 'yyyy/MM/dd') {
    return toPersianDate(date('Y-m-d'), $format, false);
}


function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verifyPassword($password, $hashedPassword) {
    if (empty($password) || empty($hashedPassword)) {
        return false;
    }
    return password_verify($password, $hashedPassword);
}

/**
 * برای پاک‌سازی ورودی‌های کاربر جهت جلوگیری از XSS.
 * @param string|array $data داده ورودی.
 * @return string|array داده پاک‌سازی شده.
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
}
?>
