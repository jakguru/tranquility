<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Role extends Model
{
    use \App\Helpers\Loggable;
    use \App\Helpers\Listable;
    use \App\Helpers\Permitable;

    public static $list_columns = [
        'name' => [
            'type' => 'text',
            'label' =>'Name',
        ],
        'created_at' => [
            'type' => 'datetime',
            'label' =>'Created',
        ],
        'updated_at' => [
            'type' => 'datetime',
            'label' =>'Updated',
        ],
    ];
    
    protected $fillable = [
        'name', 'locked'
    ];

    public function role()
    {
        return $this->hasOne('App\Role');
    }

    public function roles()
    {
        return $this->hasMany('App\Role');
    }

    public function users()
    {
        return $this->hasMany('App\User');
    }

    public static function getHierarchalCollection()
    {
        $roles = self::all();
        $collection = [];
        foreach ($roles as $role) {
            if (is_null($role->role_id)) {
                array_push($collection, $role);
            }
        }
        return collect($collection);
    }

    public static function getSelectChoices()
    {
        $choices = [];
        $hierarchied = self::getHierarchalCollection();
        foreach ($hierarchied as $role) {
            $choices[strval($role->id)] = $role->name;
            $choices = self::appendChildrenSelectChoices($role, $choices);
        }
        return $choices;
    }

    protected static function appendChildrenSelectChoices($role, $choices, $depth = 0)
    {
        $count = 0;
        foreach ($role->roles as $childrole) {
            $choices[strval($childrole->id)] = sprintf('%s%s %s', str_repeat(' ', $depth + (0 == $count ? 0 : 1 )), (0 == $count ? '⌞' : '' ), $childrole->name);
            $choices = self::appendChildrenSelectChoices($childrole, $choices, $depth + 1);
            $count ++;
        }
        return $choices;
    }
}
