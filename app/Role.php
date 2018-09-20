<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use \App\Helpers\Loggable;
    
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
