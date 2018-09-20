<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RealTimeEvent extends Model
{
    public function save(array $options = [])
    {
        return false;
    }
}
