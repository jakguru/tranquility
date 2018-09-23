<?php

namespace App\Realtime;

use Carbon\Carbon;
use \App\User;
use \App\Jobs\EmitEventToUser;

class RealtimeEvent
{
    public $type = 'notification';
    public $content = null;
    public $created_at = null;

    public function __construct($type = 'notification', $content = null)
    {
        $this->type = $type;
        $this->content = $content;
        $this->created_at = Carbon::now();
    }

    public function getHash()
    {
        return md5(print_r($this, true));
    }

    public function emitToUser($user, $delay = 0)
    {
        if (!is_a($user, '\App\User')) {
            $user = User::find($user);
            if (!is_a($user, '\App\User')) {
                return false;
            }
        }
        if (0 == intval($delay)) {
            EmitEventToUser::dispatch($user, $this);
        } else {
            EmitEventToUser::dispatch($user, $this)->delay(now()->addMinutes($delay));
        }
    }

    public function broadcastToAll()
    {
        $users = User::all();
        foreach ($users as $user) {
            $this->emitToUser($user);
        }
    }

    public static function emit($user, $type = 'notification', $content = null, $delay = 0)
    {
        $c = get_called_class();
        $e = new $c($type, $content);
        if (!is_a($user, '\App\User')) {
            $user = User::find($user);
            if (!is_a($user, '\App\User')) {
                return false;
            }
        }
        if (0 == intval($delay)) {
            EmitEventToUser::dispatch($user, $e);
        } else {
            EmitEventToUser::dispatch($user, $e, $delay);
        }
        return true;
    }

    public static function broadcast($type = 'notification', $content = null, $delay = 0)
    {
        $users = User::all();
        foreach ($users as $user) {
            self::emit($user, $type, $content, $delay);
        }
    }
}
