<?php

namespace App\Realtime\Events;

class RealtimeAlert extends \App\Realtime\RealtimeEvent
{
    public static function alert($message, $url = '#', $type = 'danger', $icon_class = 'fas fa-exclamation-triangle')
    {
        $content = new \stdClass();
        $content->message = $message;
        $content->url = $url;
        $content->type = $type;
        $content->icon_class = $icon_class;
        $c = get_called_class();
        $e = new $c('alert', $content);
        return $e;
    }

    public static function emitAlert($user, $message, $url = '#', $type = 'danger', $icon_class = 'fas fa-exclamation-triangle')
    {
        $e = self::alert($message, $url, $type, $icon_class);
        $e->emitToUser($user, 1);
    }

    public static function emitAlertToSession($user, $message, $url = '#', $type = 'danger', $icon_class = 'fas fa-exclamation-triangle')
    {
        $e = self::alert($message, $url, $type, $icon_class);
        $e->emitToCurrentSession(1);
    }
}
