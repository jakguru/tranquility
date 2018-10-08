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
    ];

    protected $translations = [];

    public function __construct()
    {
        foreach (self::$terms as $term) {
            $this->translations[$term] = __($term);
        }
    }

    public function getTranslations()
    {
        return $this->translations;
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
}
