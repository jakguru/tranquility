<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuthenticatedSession extends Model
{
    protected $casts = [
        'events' => 'array',
    ];

    protected $fillable = [
        'events', 'ip'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
