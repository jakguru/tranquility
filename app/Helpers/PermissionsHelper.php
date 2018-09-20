<?php

namespace App\Helpers;

use Illuminate\Database\Schema\Blueprint;

class PermissionsHelper
{
    public static $permissionOptions = ['none', 'own', 'all'];

    public static function getPermissionFieldsForModel($model)
    {
        $model = strtolower($model);
        return [
            sprintf('can_list_%s', $model),
            sprintf('can_add_%s', $model),
            sprintf('can_view_%s', $model),
            sprintf('can_edit_%s', $model),
            sprintf('can_delete_%s', $model),
        ];
    }

    public static function addPermissionsForModel($model, Blueprint &$table)
    {
        foreach (self::getPermissionFieldsForModel($model) as $field) {
            $table->enum($field, self::$permissionOptions)->default('none');
        }
    }

    public static function getClassName($classname)
    {
        if ($pos = strrpos($classname, '\\')) {
            return substr($classname, $pos + 1);
        }
        return $pos;
    }

    public static function onUserSaved(\App\User $user)
    {
        if ($user->wasChanged('role_id')) {
            $old_role = $user->getOriginal('role_id');
            $new_role = $user->role_id;
            \App\Jobs\UpdateOwnedUserIdsForRole::dispatch($old_role);
            \App\Jobs\UpdateOwnedUserIdsForRole::dispatch($new_role);
            if ($user->id > 0) {
                \App\Jobs\UpdateOwnedUserIds::dispatch($user);
            }
        }
    }

    public static function onRoleSaved(\App\Role $role)
    {
        if ($role->wasChanged('role_id')) {
            $old_role = $role->getOriginal('role_id');
            $new_role = $role->role_id;
            \App\Jobs\UpdateOwnedUserIdsForRole::dispatch($old_role);
            \App\Jobs\UpdateOwnedUserIdsForRole::dispatch($new_role);
            \App\Jobs\UpdateOwnedUserIdsForRole::dispatch($role->id);
        }
    }

    public static function modelIsOwnable($model)
    {
        return self::ModelHasTrait($model, 'UserOwnable');
    }

    protected static function modelHasTrait($model, $trait)
    {
        $uses = class_uses($model);
        $keys = array_keys($uses);
        $vals = array_values($uses);
        $traits = array_map([get_called_class(), 'getTraitWithoutNamespace'], $keys, $vals);
        $traits = array_unique($traits);
        return (in_array($trait, $traits));
    }

    protected static function getTraitWithoutNamespace($trait)
    {
        if (false === $lp = strrpos($trait, '\\')) {
            return $trait;
        }
        return substr($trait, $lp + 1);
    }
}
