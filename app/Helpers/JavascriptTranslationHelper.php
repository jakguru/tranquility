<?php

namespace App\Helpers;

class JavascriptTranslationHelper
{
    public static $terms = [
        'New Appointment',
        'You do not have any notifications.',
        'Schedule an Appointment',
        'Subject',
        'Start',
        'Ends',
        'Participants',
        'Description',
        'Schedule Appointment',
        'Cancel',
        'Processing',
        'Error',
        'Retry',
        'An unknown error occured while trying to create your meeting.',
        'Could not create your meeting due to the following errors:',
    ];

    public static $routes = [
        'multi-model-search',
        'create-appointment',
    ];

    protected $translations = [];

    protected $urls = [];

    public function __construct()
    {
        foreach (self::$terms as $term) {
            $this->translations[$term] = __($term);
        }
        foreach (self::$routes as $route) {
            $this->urls[$route] = route($route);
        }
    }

    public function getTranslations()
    {
        return $this->translations;
    }

    public function getRoutes()
    {
        return $this->urls;
    }

    public static function render($varName = 'tt', $render = false)
    {
        $c = get_called_class();
        $obj = new $c;
        $html = sprintf('<script type="text/javascript" id="%s">var %s = %s</script>' . "\n", $varName, $varName, json_encode($obj->getTranslations()));
        $html .= sprintf('<script type="text/javascript">function __(T){return"object"!=typeof %s||"string"!=typeof %s[T]?T:%s[T]}</script>' . "\n", $varName, $varName, $varName);
        if (true == $render) {
            echo $html;
            return null;
        } else {
            return $html;
        }
    }

    public static function renderRoutes($varName = 'tr', $render = false)
    {
        $c = get_called_class();
        $obj = new $c;
        $html = sprintf('<script type="text/javascript" id="%s">var %s = %s</script>' . "\n", $varName, $varName, json_encode($obj->getRoutes()));
        $html .= sprintf('<script type="text/javascript">function route(T){return"object"!=typeof %s||"string"!=typeof %s[T]?T:%s[T]}</script>' . "\n", $varName, $varName, $varName);
        if (true == $render) {
            echo $html;
            return null;
        } else {
            return $html;
        }
    }
}
