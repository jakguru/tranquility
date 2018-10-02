<?php

namespace App\Helpers;

switch (true) {
    case ( isset($_SERVER['HTTP_CF_CONNECTING_IP']) ):
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
        break;
    case ( isset($_SERVER['HTTP_INCAP_CLIENT_IP']) ):
        $ip = $_SERVER['HTTP_INCAP_CLIENT_IP'];
        break;
    case ( isset($_SERVER['True-Client-IP']) ):
        $ip = $_SERVER['True-Client-IP'];
        break;
    case ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) ):
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        break;
    case ( isset($_SERVER['HTTP_X_REAL_IP']) ):
        $ip = $_SERVER['HTTP_X_REAL_IP'];
        break;
    case ( isset($_SERVER['X-Forwarded-For']) ):
        $ip = $_SERVER['X-Forwarded-For'];
        break;
    case ( isset($_SERVER['X-Forwarded-For']) ):
        $ip = $_SERVER['X-Forwarded-For'];
        break;
    default:
        $cur = array_key_exists('REMOTE_ADDR', $_SERVER) ? $_SERVER['REMOTE_ADDR'] : '';
        $list = explode(',', $cur);
        $real = filter_var($list[0], FILTER_VALIDATE_IP);
        $parts = explode('.', $real);
        $ip = $real;
        break;
}

if (array_key_exists('REMOTE_ADDR', $_SERVER) && $_SERVER['REMOTE_ADDR'] !== $ip && !empty($ip)) {
    $_SERVER['REMOTE_ADDR'] = $ip;
}
