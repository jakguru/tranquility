<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use \App\Helpers\Loggable;
    
    protected $fillable = [
        'name', 'locked',
    ];

    public function users()
    {
        return $this->belongsToMany('App\User', 'group_user');
    }
}
