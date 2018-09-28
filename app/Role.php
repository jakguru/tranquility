<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use \App\Helpers\Loggable;
    use \App\Helpers\Listable;

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

    public function users()
    {
        return $this->hasMany('App\User');
    }
}
