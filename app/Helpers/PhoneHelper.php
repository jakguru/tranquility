<?php

namespace App\Helpers;

use \libphonenumber\PhoneNumberFormat;
use \libphonenumber\PhoneNumberToCarrierMapper;
use \libphonenumber\PhoneNumber;
use \libphonenumber\PhoneNumberUtil;
use \App\Helpers\CountryHelper;

class PhoneHelper
{
    public $valid = false;
    public $number = null;
    public $country = null;
    public $type = null;

    protected $util;
    protected $proto;

    public function __construct($number, $country = '')
    {
        $number = self::stripNonNumeric($number);
        if ('XX' == $country || 0 == strlen($country)) {
            $country = self::guessCountryFromPhone($number);
        }
        $country = strtoupper($country);
        $this->country = (strlen($country) > 0) ? $country : 'XX';
        $this->util = PhoneNumberUtil::getInstance();
        try {
            $this->proto = $this->util->parse($number, $this->country);
        } catch (\Exception $e) {
            return false;
        }
        $this->valid = $this->util->isValidNumber($this->proto);
        if (true == $this->valid) {
            $this->number = $number;
        }
        $this->type = self::getNumberType($this->util->getNumberType($this->proto));
    }

    public function format(string $format = 'noplus')
    {
        if (is_a($this->proto, '\libphonenumber\PhoneNumber') && true == $this->valid) {
            switch (strtolower($format)) {
                case 'local':
                    return $this->util->format($this->proto, PhoneNumberFormat::NATIONAL);
                    break;
                case 'international':
                    return $this->util->format($this->proto, PhoneNumberFormat::INTERNATIONAL);
                    break;
                case 'e164':
                    return $this->util->format($this->proto, PhoneNumberFormat::E164);
                    break;
                case 'noplus':
                    return substr($this->util->format($this->proto, PhoneNumberFormat::E164), 1);
                    break;
            }
        }
        return $this->number;
    }

    public function getCarrier()
    {
        $cm = PhoneNumberToCarrierMapper::getInstance();
        if (is_a($cm, 'libphonenumber\PhoneNumberToCarrierMapper') && is_a($this->proto, '\libphonenumber\PhoneNumber') && true == $this->valid) {
            return $cm->getNameForNumber($this->proto, 'en');
        }
        return '';
    }

    public static function stripNonNumeric($input)
    {
        return preg_replace('/[^0-9]/', '', trim($input));
    }

    public static function guessCountryFromPhone($phone)
    {
        $phone = self::stripNonNumeric($phone);
        $countriesByCode = CountryHelper::getCountriesByCountryCode();
        $iso = 'XX';
        foreach ($countriesByCode as $code => $countries) {
            if (starts_with($code, $phone)) {
                $iso = array_shift($countries);
                break;
            }
        }
        return $iso;
    }

    public static function getNumberType(int $val = 1000)
    {
        $types = array(
            0 => 'LANDLINE',
            1 => 'MOBILE',
            2 => 'LANDLINE_OR_MOBILE',
            3 => 'TOLL_FREE',
            4 => 'PREMIUM_RATE',
            5 => 'SHARED_COST',
            6 => 'VOIP',
            7 => 'PERSONAL_NUMBER',
            8 => 'PAGER',
            9 => 'UAN',
            10 => 'UNKNOWN',
            27 => 'EMERGENCY',
            28 => 'VOICEMAIL',
            29 => 'SHORT_CODE',
            30 => 'STANDARD_RATE',
        );
        return self::getArrayKey($val, $types, 'UNKNOWN');
    }

    public static function isValidPhone($number, $country = '')
    {
        $c = get_called_class();
        $obj = new $c($number, $country);
        return $obj->valid;
    }

    public static function isValidPhoneOfType($number, $country = '', $types = [])
    {
        $c = get_called_class();
        $obj = new $c($number, $country);
        if (true !== $obj->valid) {
            return false;
        }
        return in_array($obj->type, $types);
    }

    public static function isValidMobilePhone($number, $country, $strict = false)
    {
        $check = (true == $strict) ? ['MOBILE'] : ['MOBILE', 'LANDLINE_OR_MOBILE'];
        return self::isValidPhoneOfType($number, $country, $check);
    }

    public static function formatPhone($number, $country, $format = 'noplus')
    {
        $c = get_called_class();
        $obj = new $c($number, $country);
        return $obj->format($format);
    }

    public static function isValidPhoneValidator($attribute, $value, $parameters, $validator)
    {
        $validator_data = $validator->getData();
        $country_key = sprintf('%s_country', $attribute);
        $country = array_get($validator_data, $country_key, '');
        return self::isValidPhone($value, $country);
    }

    public static function isValidMobilePhoneValidator($attribute, $value, $parameters, $validator)
    {
        $validator_data = $validator->getData();
        $country_key = sprintf('%s_country', $attribute);
        $country = array_get($validator_data, $country_key, '');
        return self::isValidMobilePhone($value, $country);
    }

    public static function isValidMobilePhoneStrictValidator($attribute, $value, $parameters, $validator)
    {
        $validator_data = $validator->getData();
        $country_key = sprintf('%s_country', $attribute);
        $country = array_get($validator_data, $country_key, '');
        return self::isValidMobilePhone($value, $country, true);
    }

    protected static function getArrayKey($key, $array, $default = null)
    {
        return (!is_array($array) || !array_key_exists($key, $array)) ? $default: $array[$key];
    }
}
