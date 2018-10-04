<?php

namespace App\Http\Middleware;

use Closure;
use \App;

class SetUserLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (is_a($request->user(), '\App\User') && in_array($request->user()->locale, array_keys(\App\Http\Controllers\SettingsController::getListOfLanguages()))) {
            App::setLocale($request->user()->locale);
        }
        return $next($request);
    }
}
