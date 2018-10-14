<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

// use \App\Helpers\DebugLoggable;

trait DebugLoggable
{
    protected static function log($what, $type = 'info', $force = false)
    {
        if (true == config('app.debug') || true == $force) {
            forward_static_call(['Log', $type], $what);
        }
    }
}
