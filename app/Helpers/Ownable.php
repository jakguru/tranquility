<?php

namespace App\Helpers;

// use \App\Helpers\Ownable;

trait Ownable
{
    public function creator()
    {
        return $this->hasOne('App\User');
    }

    public function owner()
    {
        return $this->hasOne('App\User');
    }

    public function group()
    {
        return $this->hasOne('App\Group');
    }
}
