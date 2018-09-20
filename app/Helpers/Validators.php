<?php

namespace App\Helpers;

class Validators
{
    public static function is_cidr($cidr)
    {
        $parts = explode('/', $cidr);
        if (count($parts) != 2) {
            return false;
        }
        list($ip, $netmask) = explode('/', $cidr, 2);
        $netmask = intval($netmask);
        if ($netmask < 0) {
            return false;
        }
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return $netmask <= 32;
        }
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return $netmask <= 128;
        }
        return false;
    }

    public static function is_ip($ip)
    {
        return (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) || filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6));
    }

    public static function is_ipv4($ip)
    {
        if (self::is_cidr($ip)) {
            list($ip, $netmask) = explode('/', $ip, 2);
        }
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }

    public static function is_ipv6($ip)
    {
        if (self::is_cidr($ip)) {
            list($ip, $netmask) = explode('/', $ip, 2);
        }
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
    }

    public static function in_cidr($ip, $cidr)
    {
        if (!self::is_cidr($cidr)) {
            return false;
        }
        if (self::is_ipv4($ip) && self::is_ipv4($cidr)) {
            list ($subnet, $bits) = explode('/', $cidr);
            $ip = ip2long($ip);
            $subnet = ip2long($subnet);
            $mask = -1 << ( 32 - $bits );
            $subnet &= $mask;
            return ( $ip & $mask ) == $subnet;
        } elseif (self::is_ipv6($ip) && self::is_ipv6($cidr)) {
            $ip = inet_pton($ip);
            $binaryip = self::inet_to_bits($ip);
            list($net,$maskbits) = explode('/', $cidrnet);
            $net = inet_pton($net);
            $binarynet = self::inet_to_bits($net);
            $ip_net_bits = substr($binaryip, 0, $maskbits);
            $net_bits = substr($binarynet, 0, $maskbits);
            return ($ip_net_bits == $net_bits );
        }
        return false;
    }

    public static function ips_match($a, $b)
    {
        $a = trim($a);
        $b = trim($b);
        if (self::is_ipv4($a) && self::is_ipv4($b)) {
            return (ip2long($a) == ip2long($b));
        } elseif (self::is_ipv6($a) && self::is_ipv6($b)) {
            return (inet_pton($a) == inet_pton($b));
        }
        return false;
    }

    protected static function inet_to_bits($inet)
    {
        $unpacked = unpack('A16', $inet);
        $unpacked = str_split($unpacked[1]);
        $binaryip = '';
        foreach ($unpacked as $char) {
            $binaryip .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
        }
        return $binaryip;
    }
}
