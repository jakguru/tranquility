<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use \App\Helpers\Loggable;
    use \App\Helpers\Listable;

    public static $list_columns = [
        'name' => [
            'type' => 'text',
            'label' =>'Name',
        ],
        'sudo' => [
            'type' => 'boolean',
            'label' =>'Super Admins',
        ],
        'users' => [
            'type' => 'submodulecount',
            'label' =>'Users',
        ],
        'ip_whitelist' => [
            'type' => 'linebreaklist',
            'label' => 'Whitelisted IPs',
        ],
        'ip_whitelist' => [
            'type' => 'linebreaklist',
            'label' => 'Whitelisted IPs',
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
        'name', 'locked',
    ];

    public function users()
    {
        return $this->belongsToMany('App\User', 'group_user');
    }
}
