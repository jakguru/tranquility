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
        return array_merge($this->hidden, $this->getNotLoggableFields());
    }

    public function getNotLoggableFields()
    {
        return (is_array($this->notLoggable)) ? $this->notLoggable : [];
    }
}
