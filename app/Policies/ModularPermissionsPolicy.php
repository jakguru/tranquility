<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\PermissionsHelper;

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

    public function list(User $user, $model)
    {
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

    public function add(User $user, $model)
    {
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
}
