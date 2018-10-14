<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\PermissionsHelper;
use Illuminate\Support\Facades\Log;

class ModularPermissionsPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function list(User $user)
    {
        $model = self::getCallingModel();
        if (false === $model) {
            return false;
        }
        // with the list, we need to check each item to ensure that we are not exposing something which the end user shouldn't see.
        if ($user->isSudo()) {
            return true;
        }
        $modelClass = self::getModelClass($model);
        $permission = $user->getPermissionForVerb($modelClass, 'list');
        if ('none' == $permission) {
            return false;
        } elseif ('all' == $permission) {
            return true;
        } elseif ('own' == $permission && ! PermissionsHelper::modelIsOwnable($model)) {
            return true;
        } else {
            return true;
        }
    }

    public function add(User $user)
    {
        $model = self::getCallingModel();
        if (false === $model) {
            return false;
        }
        // you can always add your own model, unless specifically denied
        if ($user->isSudo()) {
            return true;
        }
        $modelClass = self::getModelClass($model);
        $permission = $user->getPermissionForVerb($modelClass, 'add');
        if ('none' == $permission) {
            return false;
        } elseif ('all' == $permission) {
            return true;
        } elseif ('own' == $permission && ! PermissionsHelper::modelIsOwnable($model)) {
            return true;
        } else {
            return true;
        }
    }

    public function view(User $user, Model $model)
    {
        if ($user->isSudo()) {
            return true;
        }
        $modelClass = self::getModelClass($model);
        $permission = $user->getPermissionForVerb($modelClass, 'view');
        if ('none' == $permission) {
            return false;
        } elseif ('all' == $permission) {
            return true;
        } elseif ('own' == $permission && ! PermissionsHelper::modelIsOwnable($model)) {
            return true;
        } else {
            // check ownership permissions.
        }
    }

    public function edit(User $user, Model $model)
    {
        if ($user->isSudo()) {
            return true;
        }
        $modelClass = self::getModelClass($model);
        $permission = $user->getPermissionForVerb($modelClass, 'edit');
        if ('none' == $permission) {
            return false;
        } elseif ('all' == $permission) {
            return true;
        } elseif ('own' == $permission && ! PermissionsHelper::modelIsOwnable($model)) {
            return true;
        } else {
            // check ownership permissions.
        }
    }

    public function delete(User $user, Model $model)
    {
        if ($user->isSudo()) {
            return true;
        }
        $modelClass = self::getModelClass($model);
        $permission = $user->getPermissionForVerb($modelClass, 'delete');
        if ('none' == $permission) {
            return false;
        } elseif ('all' == $permission) {
            return true;
        } elseif ('own' == $permission && ! PermissionsHelper::modelIsOwnable($model)) {
            return true;
        } else {
            // check ownership permissions.
        }
    }

    protected static function getCallingModel()
    {
        foreach (debug_backtrace(false) as $trace) {
            $function = (is_array($trace) && array_key_exists('function', $trace)) ? $trace['function'] : null;
            $class = (is_array($trace) && array_key_exists('class', $trace)) ? $trace['class'] : null;
            $args = (is_array($trace) && array_key_exists('args', $trace)) ? $trace['args'] : [];
            if ('Illuminate\Foundation\Auth\User' == $class && 'can' == $function && 2 == count($args)) {
                list($method, $class) = $args;
                return $class;
            }
        }
        return false;
    }

    protected static function getModelClass($model)
    {
        if (is_a($model, '\Illuminate\Database\Eloquent\Model')) {
            $class = get_class();
            if (false !== $lp = strrpos($class, '\\')) {
                $class = substr($class, $lp + 1);
            }
        } else {
            $class = $model;
        }
        return $class;
    }

    protected static function log($what, $type = 'info', $force = false)
    {
        if (true == config('app.debug') || true == $force) {
            forward_static_call(['Log', $type], $what);
        }
    }
}
