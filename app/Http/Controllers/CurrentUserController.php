<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Rules\Google2FA;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;

class CurrentUserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function saveGoogle2FA(Request $request)
    {
        Validator::make($request->all(), [
            'code' => [
                'required',
                new Google2FA
            ],
            'origin' => 'nullable|string',
        ])->validate();
        $user = $request->user();
        $user->google2fa_secret = $request->session()->get('google2fa_secret');
        $user->save();
        return Redirect::to($request->input('origin'));
    }

    public function validateGoogle2FA(Request $request)
    {
        Validator::make($request->all(), [
            'code' => [
                'required',
                new Google2FA
            ],
            'origin' => 'nullable|string',
        ])->validate();
        $session_id = $request->session()->getId();
        session(['2favalidation' => Crypt::encrypt($session_id)]);
        return Redirect::to($request->input('origin'));
    }
}
