<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class LoggableEventHelper
{
    public function logLogin(Model $model)
    {
        self::saveActivity($model, 'Login', []);
    }

    public function logCreated(Model $model)
    {
        self::saveActivity($model, 'Creation', []);
    }

    public function logSaved(Model $model)
    {
        $changes = [];
        if ($model->wasChanged()) {
            $new_values = $model->getChanges();
            foreach ($new_values as $key => $new_value) {
                $old_value = $model->getOriginal($key);
                $changes[$key] = [
                    'old' => $old_value,
                    'new' => $new_value,
                ];
            }
            $hidden = $model->getHiddenFields();
            foreach ($hidden as $field) {
                unset($changes[$field]);
            }
            if (count($changes) > 0) {
                self::saveActivity($model, 'Update', $changes);
            }
        }
    }

    public function logDeleted(Model $model)
    {
        $raw_activity = [

        ];
    }

    public function logRestored(Model $model)
    {
        $raw_activity = [

        ];
    }

    public static function saveActivity(Model $model, $action, array $changes = [])
    {
        $current_user = Request::user();
        $current_ip = Request::ip();
        return \App\Jobs\SaveActivityLog::dispatch($model, $action, $changes, $current_user, $current_ip);
    }
}
