<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;

class CheckLogin2FA
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
        if (is_a($request->user(), '\App\User')
            && ! $this->hasValidMFALogin($request)
            && !in_array($request->route()->getName(), ['logout', 'validate-google2fa', 'save-google2fa'])
        ) {
            return new Response(view('app.layouts.google2fa.validate'));
        }
        return $next($request);
    }

    protected function hasValidMFALogin($request)
    {
        $session_id = $request->session()->getId();
        $encrypted = $request->session()->get('2favalidation');
        if (strlen($encrypted) > 0 && $session_id == Crypt::decrypt($encrypted)) {
            return true;
        }
        return false;
    }
}