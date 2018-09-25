<?php

namespace App\Helpers;

use \App\Options;

class GoogleReCAPCHAHelper
{
    public static function enabled()
    {
        return self::getReCAPTCHAConfig()->enabled;
    }

    public static function injectJS()
    {
        if (self::enabled()) {
            echo "\n";
            echo '<script src="https://www.google.com/recaptcha/api.js"></script>';
        }
    }

    public static function injectButton($classes = '', $text = null)
    {
        if (self::enabled()) {
            if (is_null($text)) {
                $text = __('Submit');
            }
            echo '<input type="hidden" name="g-recaptcha" value="" />' . "\n";
            echo sprintf('<input class="g-recaptcha %s" data-sitekey="%s" data-callback="handleGoogleReCAPCHA" role="submit" type="submit" value="%s" />', $classes, self::getReCAPTCHAConfig()->key, $text) . "\n";
        }
    }

    protected static function getReCAPTCHAConfig()
    {
        $return = new \stdClass();
        $return->key = '';
        $return->secret = '';
        $return->enabled = false;
        $google_config = Options::get('google');
        if (is_object($google_config) && property_exists($google_config, 'recapcha') && is_array($google_config->recapcha)) {
            $return->key = (array_key_exists('key', $google_config->recapcha)) ? $google_config->recapcha['key'] : '';
            $return->secret = (array_key_exists('secret', $google_config->recapcha)) ? $google_config->recapcha['secret'] : '';
            $return->enabled = (array_key_exists('enabled', $google_config->recapcha)) ? (true == $google_config->recapcha['enabled']) : false;
        }
        return $return;
    }
}
