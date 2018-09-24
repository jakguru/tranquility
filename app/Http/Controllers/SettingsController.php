<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Mail;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function lobby(Request $request)
    {
        if (!is_a($request->user(), '\App\User') || !$request->user()->isSudo()) {
            abort(401);
        }
        return view('app.layouts.settings.lobby');
    }

    public function email(Request $request)
    {
        if (!is_a($request->user(), '\App\User') || !$request->user()->isSudo()) {
            abort(401);
        }
        return view('app.layouts.settings.email');
    }

    public function google(Request $request)
    {
        if (!is_a($request->user(), '\App\User') || !$request->user()->isSudo()) {
            abort(401);
        }
        return view('app.layouts.settings.google');
    }

    public function twilio(Request $request)
    {
        if (!is_a($request->user(), '\App\User') || !$request->user()->isSudo()) {
            abort(401);
        }
        return view('app.layouts.settings.twilio');
    }

    public function saveSettings(Request $request)
    {
        switch ($request->input('section')) {
            case 'email':
                Validator::make($request->all(), [
                    'hostname' => 'required|string',
                    'port' => 'required|numeric|between:1,65535',
                    'encryption' => [
                        'nullable',
                        Rule::in(['', 'ssl', 'tls'])
                    ],
                    'username' => 'nullable|string',
                    'password' => 'nullable|string',
                    'sendermail' => 'required|email',
                    'sendername' => 'required|string',
                ])->validate();
                $option = new \stdClass();
                $option->hostname = $request->input('hostname');
                $option->port = $request->input('port');
                $option->encryption = $request->input('encryption');
                $option->username = $request->input('username');
                $option->password = $request->input('password');
                $option->sendermail = $request->input('sendermail');
                $option->sendername = $request->input('sendername');
                $saved = \App\Options::set('mail', $option);
                if (true == $saved) {
                    return Redirect::route('settings-email')->with('globalsuccessmessage', __('Updated Email Settings successfully.'));
                }
                return Redirect::route('settings-email')->with('globalerrormessage', __('Failed to updated Email Settings.'));
                break;

            case 'test-email':
                $enqueued = Mail::to($request->input('recipient'))->queue(new \App\Mail\TestMailer($request->input('subject'), $request->input('message')));
                if (!$enqueued) {
                    return Redirect::route('settings-email')->with('globalerrormessage', __('Failed to enqueue email.'));
                }
                return Redirect::route('settings-email')->with('globalsuccessmessage', __('Enqueued email successfully.'));
                break;
            
            default:
                return Redirect::route('settings')->with('globalerrormessage', sprintf(__('Invalid Setting Section Name "%s"'), $request->input('section')));
                break;
        }
    }
}
