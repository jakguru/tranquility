<?php

namespace App\Helpers;

// use \App\Helpers\Ownable;

trait Ownable
{
    public function owner()
    {
        return $this->hasOne('App\User', 'id', 'owner_id');
    }
}
