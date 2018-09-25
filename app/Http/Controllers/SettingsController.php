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
        return view('app.layouts.settings.google', ['settings' => \App\Options::get('google')]);
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

            case 'google-recapcha':
                $data = $request->input('recapcha');
                Validator::make($data, [
                    'key' => 'string',
                    'secret' => 'string|required_with:key',
                    'enabled' => 'nullable|accepted',
                ]);
                $settings = \App\Options::get('google');
                if (!is_object($settings)) {
                    $settings = new \stdClass();
                }
                if (!property_exists($settings, 'recapcha')) {
                    $settings->recapcha = [];
                }
                $settings->recapcha['key'] = $data['key'];
                $settings->recapcha['secret'] = $data['secret'];
                $settings->recapcha['enabled'] = array_key_exists('enabled', $data);
                $saved = \App\Options::set('google', $settings);
                if (true == $saved) {
                    return Redirect::route('settings-google')->with('globalsuccessmessage', __('Updated Google ReCAPCHA Settings successfully.'));
                }
                return Redirect::route('settings-google')->with('globalerrormessage', __('Failed to updated Google ReCAPCHA Settings.'));
                break;

            case 'google-maps':
                $data = $request->input('maps');
                Validator::make($data, [
                    'key' => 'string',
                    'enabled' => 'nullable|accepted',
                ]);
                $settings = \App\Options::get('google');
                if (!is_object($settings)) {
                    $settings = new \stdClass();
                }
                if (!property_exists($settings, 'maps')) {
                    $settings->maps = [];
                }
                $settings->maps['key'] = $data['key'];
                $settings->maps['enabled'] = array_key_exists('enabled', $data);
                $saved = \App\Options::set('google', $settings);
                if (true == $saved) {
                    return Redirect::route('settings-google')->with('globalsuccessmessage', __('Updated Google Maps Settings successfully.'));
                }
                return Redirect::route('settings-google')->with('globalerrormessage', __('Failed to updated Google Maps Settings.'));
                break;
            
            default:
                return Redirect::route('settings')->with('globalerrormessage', sprintf(__('Invalid Setting Section Name "%s"'), $request->input('section')));
                break;
        }
    }
}
