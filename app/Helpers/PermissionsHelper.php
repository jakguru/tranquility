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

    // \App\Helpers\PermissionsHelper::addFieldsForOwnable($table);
    public static function addFieldsForOwnable(Blueprint &$table)
    {
        $table->integer('creator_id')->unsigned()->nullable();
        $table->integer('owner_id')->unsigned()->nullable();
        $table->integer('group_id')->unsigned()->nullable();
        $table->foreign('creator_id')->references('id')->on('users')
              ->onDelete('cascade')
              ->onUpdate('cascade');
        $table->foreign('owner_id')->references('id')->on('users')
              ->onDelete('cascade')
              ->onUpdate('cascade');
        $table->foreign('group_id')->references('id')->on('groups')
              ->onDelete('cascade')
              ->onUpdate('cascade');
    }

    // \App\Helpers\PermissionsHelper::removeFieldsForOwnable($table);
    public static function removeFieldsForOwnable(Blueprint &$table)
    {
        $table = $table->getTable();
        $table->dropForeign(sprintf('%s_creator_id_foreign', $table));
        $table->dropForeign(sprintf('%s_owner_id_foreign', $table));
        $table->dropForeign(sprintf('%s_group_id_foreign', $table));
    }

    public static function modelHasTrait($model, $trait)
    {
        if (!class_exists($model)) {
            return false;
        }
        $uses = class_uses($model);
        $keys = array_keys($uses);
        $vals = array_values($uses);
        $traits = array_map([get_called_class(), 'getTraitWithoutNamespace'], $keys, $vals);
        $traits = array_unique($traits);
        return (in_array($trait, $traits));
    }

    public static function getPermitableModels($namespace = '\\App')
    {
        $return = [];
        $path = app_path();
        try {
            $df = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::SELF_FIRST);
            foreach ($df as $item) {
                if ($item->isReadable() && $item->isFile() && mb_strtolower($item->getExtension()) === 'php') {
                    $class = str_replace("/", "\\", mb_substr($item->getRealPath(), mb_strlen($path), -4));
                    $class = sprintf('%s%s', ('\\' == substr($namespace, -1)) ? substr($namespace, 0, strlen($namespace) - 1) : $namespace, $class);
                    array_push($return, $class);
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
        $return = array_filter($return, function ($class) {
            return self::modelHasTrait($class, 'Permitable');
        });
        return $return;
    }

    protected static function getTraitWithoutNamespace($trait)
    {
        if (false === $lp = strrpos($trait, '\\')) {
            return $trait;
        }
        return substr($trait, $lp + 1);
    }
}
