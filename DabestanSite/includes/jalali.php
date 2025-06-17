<?php
// File: DabestanSite/includes/jalali.php
// Final corrected version for Jalali date calculation.

function jdate($format, $timestamp = '', $none = '', $time_zone = 'Asia/Tehran', $tr_num = 'fa')
{
    $T_sec = 0;
    if ($time_zone != 'local') date_default_timezone_set(($time_zone === '') ? 'Asia/Tehran' : $time_zone);
    $ts = $T_sec + (($timestamp === '') ? time() : tr_num($timestamp));
    $date = explode('_', date('H_i_j_n_O_P_s_w_Y', $ts));
    list($j_y, $j_m, $j_d) = gregorian_to_jalali($date[8], $date[3], $date[2]);
    $doy = ($j_m < 7) ? (($j_m - 1) * 31) + $j_d - 1 : (($j_m - 7) * 30) + $j_d + 185;
    $kab = ($j_y % 33 % 4 == 1) ? 1 : 0;
    $sl = strlen($format);
    $out = '';
    for ($i = 0; $i < $sl; $i++) {
        $sub = substr($format, $i, 1);
        if ($sub == '\\') {
            $out .= substr($format, ++$i, 1);
            continue;
        }
        switch ($sub) {
            case 'a': $out .= ($date[0] < 12) ? 'ق.ظ' : 'ب.ظ'; break;
            case 'A': $out .= ($date[0] < 12) ? 'قبل از ظهر' : 'بعد از ظهر'; break;
            case 'b': $out .= (int)($j_m / 3.1) + 1; break;
            case 'd': $out .= ($j_d < 10) ? '0' . $j_d : $j_d; break;
            case 'f': $out .= jdate_words(array('kh' => $j_m)); break;
            case 'F': $out .= jdate_words(array('mm' => $j_m)); break;
            case 'g': $out .= date('g', $ts); break;
            case 'G': $out .= date('G', $ts); break;
            case 'h': $out .= date('h', $ts); break;
            case 'H': $out .= date('H', $ts); break;
            case 'i': $out .= date('i', $ts); break;
            case 'j': $out .= $j_d; break;
            case 'J': $out .= jdate_words(array('rr' => $j_d)); break;
            case 'l': $out .= jdate_words(array('rh' => $date[7])); break;
            case 'L': $out .= $kab; break;
            case 'm': $out .= ($j_m < 10) ? '0' . $j_m : $j_m; break;
            case 'M': $out .= jdate_words(array('km' => $j_m)); break;
            case 'n': $out .= $j_m; break;
            case 's': $out .= date('s', $ts); break;
            case 'S': $out .= 'م'; break;
            case 't': $out .= ($j_m != 12) ? (31 - (int)($j_m / 6.5)) : ($kab ? 30 : 29); break;
            case 'w': $out .= ($date[7] == 6) ? 0 : $date[7] + 1; break;
            case 'y': $out .= substr($j_y, 2, 2); break;
            case 'Y': $out .= $j_y; break;
            case 'Z': $out .= $doy; break;
            default: $out .= $sub;
        }
    }
    return ($tr_num != 'en') ? tr_num($out, 'fa', '.') : $out;
}

function jdate_words($array)
{
    foreach ($array as $type => $num) {
        $num = (int)tr_num($num);
        switch ($type) {
            case 'mm':
                $key = array('فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند');
                $out = $key[$num - 1];
                break;
            case 'rh':
                $key = array('یکشنبه', 'دوشنبه', 'سه شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه', 'شنبه');
                $out = $key[$num];
                break;
            case 'km':
                $key = array('فر', 'ار', 'خر', 'تی', 'مر', 'شه', 'مه', 'آب', 'آذ', 'دی', 'به', 'اس');
                $out = $key[$num - 1];
                break;
            case 'kh':
                $key = array('حمل', 'ثور', 'جوزا', 'سرطان', 'اسد', 'سنبله', 'میزان', 'عقرب', 'قوس', 'جدی', 'دلو', 'حوت');
                $out = $key[$num - 1];
                break;
        }
    }
    return $out;
}

function gregorian_to_jalali($gy, $gm, $gd)
{
    $g_d_m = array(0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334);
    $jy = ($gy <= 1600) ? 0 : 979;
    $gy -= ($gy <= 1600) ? 621 : 1600;
    $gy2 = ($gm > 2) ? ($gy + 1) : $gy;
    $days = (365 * $gy) + ((int)(($gy2 + 3) / 4)) - ((int)(($gy2 + 99) / 100)) + ((int)(($gy2 + 399) / 400)) - 80 + $gd + $g_d_m[$gm - 1];
    $jy += 33 * ((int)($days / 12053));
    $days %= 12053;
    $jy += 4 * ((int)($days / 1461));
    $days %= 1461;
    $jy += (int)(($days - 1) / 365);
    if ($days > 365) $days = ($days - 1) % 365;
    $jm = ($days < 186) ? 1 + (int)($days / 31) : 7 + (int)(($days - 186) / 30);
    $jd = 1 + (($days < 186) ? ($days % 31) : (($days - 186) % 30));
    return array($jy, $jm, $jd);
}

function tr_num($str, $mod = 'en', $mf = '٫')
{
    $num_a = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '.');
    $num_b = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹', $mf);
    return ($mod == 'fa') ? str_replace($num_a, $num_b, $str) : str_replace($num_b, $num_a, $str);
}