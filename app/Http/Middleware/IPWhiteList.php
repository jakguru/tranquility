<?php

namespace App\Http\Middleware;

use \Illuminate\Http\Request;
use \Illuminate\Support\Facades\Auth;
use \Illuminate\Auth\AuthenticationException;
use Closure;

class IPWhiteList
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $ip = $request->ip();
        if (is_a($user, '\App\User') && !$user->canUseIp($ip)) {
            \App\Jobs\SaveActivityLog::dispatch($user, 'Invalid IP Attempt', [], $user, $ip);
            app('\App\Http\Controllers\Auth\LoginController')->logout($request);
            return redirect('/login')->with('errormessage', sprintf(__('You are not allowed to use this application from IP %s. This attempt has been logged.'), $ip));
        }
        return $next($request);
    }
}
