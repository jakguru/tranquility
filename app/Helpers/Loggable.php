<?php

namespace App\Helpers;

trait Loggable
{
    public function activities()
    {
        return $this->morphMany('App\Activity', 'model');
    }

    public function getHiddenFields()
    {
    	return $this->hidden;
    }
}
