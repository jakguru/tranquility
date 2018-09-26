<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class Enforce2FA
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
            && $request->user()->isSudo()
            && 0 === strlen($request->user()->google2fa_secret)
            && !in_array($request->route()->getName(), ['logout', 'save-google2fa', 'validate-google2fa',])
        ) {
            $google2fa = app('pragmarx.google2fa');
            if ($request->session()->has('google2fa_secret')) {
                $secret = $request->session()->get('google2fa_secret');
            } else {
                $secret = $google2fa->generateSecretKey();
            }
            $qri = $google2fa->getQRCodeInline(
                config('app.name'),
                config('app.url'),
                $secret
            );
            $request->session()->flash('google2fa_secret', $secret);
            return new Response(view('app.layouts.google2fa.register', [
                'base_template' => 'app.blueprints.frameless',
                'qri' => $qri,
                'secret' => $secret,
            ]));
        }
        return $next($request);
    }
}
