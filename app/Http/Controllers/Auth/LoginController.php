<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use \App\Helpers\LoggableEventHelper;
use \App\Http\Controllers\AuthenticatedSessionController;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('app.layouts.login');
    }

    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    protected function authenticated(Request $request, $user)
    {
        $hasher = app('hash');
        AuthenticatedSessionController::onLogin();
        LoggableEventHelper::saveActivity($user, 'Login');
        if ($user->email == 'sudo@localhost.local') {
            \App\Realtime\Events\RealtimeAlert::emitAlertToSession($user, 'You are using the default installation email. Please consider changing it.', '#', 'danger', 'fas fa-exclamation-triangle', 10);
            if ($hasher->check('admin', $user->password)) {
                \App\Realtime\Events\RealtimeAlert::emitAlertToSession($user, 'You are using the default installation password. Please consider changing it.', '#', 'danger', 'fas fa-exclamation-triangle', 10);
            }
        }
        $le = new \App\Realtime\RealtimeEvent('login');
        $le->emitToCurrentSession(5);
    }

    public function logout(Request $request)
    {
        AuthenticatedSessionController::onLogout();
        LoggableEventHelper::saveActivity($request->user(), 'Logout');
        $this->guard()->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect('/');
    }
}
