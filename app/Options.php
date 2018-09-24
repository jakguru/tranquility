<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Options extends Model
{
    use \App\Helpers\Loggable;

    protected $notLoggable = [
        'created_at', 'updated_at',
    ];

    public static function set($key, $value)
    {
        $c = get_called_class();
        $option = $c::where(['option_key' => $key])->first();
        if (!is_a($option, '\App\Options')) {
            $option = new $c;
            $option->option_key = $key;
        }
        $option->option_value = serialize($value);
        $option->save();
        if (is_a($option, '\App\Options') && $option->id > 0) {
            $cache_key = sprintf('option.%s', $key);
            Cache::forever($cache_key, $value);
            return true;
        }
        return false;
    }

    public static function get($key, $default = null)
    {
        $cache_key = sprintf('option.%s', $key);
        if (Cache::has($cache_key)) {
            return Cache::get($cache_key, $default);
        }
        $c = get_called_class();
        $option = $c::where(['option_key' => $key])->first();
        if (!is_a($option, '\App\Options')) {
            return $default;
        }
        return unserialize($option->option_value);
    }
}
