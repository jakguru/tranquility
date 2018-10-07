<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class MyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function inbox(Request $request)
    {
        abort(501);
    }

    public function calendar(Request $request)
    {
        $timezone = (!is_null($request->user()->timezone)) ? $request->user()->timezone : config('app.timezone');
        $params = new \stdClass();
        $params->view = 'day';
        $params->year = intval($request->input('year', date('Y')));
        $params->month = intval($request->input('month', date('m')));
        $params->date = new Carbon($request->input('date', date('Y-m-d')), $timezone);
        $params->cmonth = Carbon::createFromDate($params->year, $params->month);
        $params->items = [];
        $params->timezone = $timezone;
        $days = array_keys(\App\Http\Controllers\SettingsController::getListOfDays());
        $first_day = null;
        $params->days = [];
        $attcount = 0;
        while (count($params->days) < 7 && $attcount < 100) {
            $day = array_shift($days);
            if (is_null($first_day) && $day !== config('app.beginningofweek', 'monday')) {
                array_push($days, $day);
            } elseif ($day == config('app.beginningofweek', 'monday')) {
                $first_day = $day;
                array_push($params->days, $day);
            } elseif (!is_null($first_day)) {
                array_push($params->days, $day);
            }
            $attcount ++;
        }
        return view('app.layouts.my.calendar', ['params' => $params]);
    }

    public function preferences(Request $request)
    {
        return view('app.layouts.my.preferences', ['model' => $request->user()]);
    }

    public function savePreferences(Request $request)
    {
        $section = $request->input('section');
        switch ($section) {
            case 'preferences':
                $rules = [
                    'avatar' => ['nullable','string', new \App\Rules\Base64EncodedImage],
                    'salutation' => 'string|nullable',
                    'title' => 'string|nullable',
                    'fName' => 'required|string',
                    'lName' => 'required|string',
                    'email' => [
                        'required',
                        'email',
                        Rule::unique('users')->ignore($request->user()->id),
                    ],
                    'main_phone_country' => ['required_with:main_phone', Rule::in(\App\Helpers\CountryHelper::getCountriesForValidation(false))],
                    'main_phone' => 'phone|nullable',
                    'mobile_phone_country' => ['required_with:mobile_phone', Rule::in(\App\Helpers\CountryHelper::getCountriesForValidation(false))],
                    'mobile_phone' => 'phone|mobile|nullable',
                    'home_phone_country' => ['required_with:home_phone', Rule::in(\App\Helpers\CountryHelper::getCountriesForValidation(false))],
                    'home_phone' => 'phone|nullable',
                    'work_phone_country' => ['required_with:work_phone', Rule::in(\App\Helpers\CountryHelper::getCountriesForValidation(false))],
                    'work_phone' => 'phone|nullable',
                    'fax_phone_country' => ['required_with:fax_phone', Rule::in(\App\Helpers\CountryHelper::getCountriesForValidation(false))],
                    'fax_phone' => 'phone|nullable',
                    'birthday' => 'date|nullable|before_or_equal:18 years ago',
                    'address_line_1' => 'string|nullable',
                    'address_line_2' => 'string|nullable',
                    'city' => 'string|nullable',
                    'state' => 'string|nullable',
                    'postal' => 'string|nullable',
                    'country' => ['string', 'required', Rule::in(\App\Helpers\CountryHelper::getCountriesForValidation(false))],
                    'whatsapp_phone_country' => ['required_with:whatsapp_phone', Rule::in(\App\Helpers\CountryHelper::getCountriesForValidation(false))],
                    'whatsapp_phone' => 'phone|nullable',
                    'telegram_phone_country' => ['required_with:telegram_phone', Rule::in(\App\Helpers\CountryHelper::getCountriesForValidation(false))],
                    'telegram_phone' => 'phone|nullable',
                    'viber_phone_country' => ['required_with:viber_phone', Rule::in(\App\Helpers\CountryHelper::getCountriesForValidation(false))],
                    'viber_phone' => 'phone|nullable',
                    'skype' => 'string|nullable',
                    'facebook' => 'url|nullable',
                    'googleplus' => 'url|nullable',
                    'linkedin' => 'url|nullable',
                    'timezone' => ['required', 'string', Rule::in(\DateTimeZone::listIdentifiers(\DateTimeZone::ALL))],
                    'locale' => ['required', 'string', Rule::in(array_keys(\App\Http\Controllers\SettingsController::getListOfLanguages()))],
                    'temperature_unit' => ['required', 'string', Rule::in(['celsius', 'fahrenheit'])],
                    'dateformat' => ['required', 'string'],
                    'timeformat' => ['required', 'string'],
                    'datetimeformat' => ['required', 'string'],
                ];
                Validator::make($request->all(), $rules)->validate();
                if ($request->has('avatar') && !empty($request->input('avatar'))) {
                    $request->merge(['avatar' => \App\Helpers\ModelImageHelper::saveImageFromBase64($request->input('avatar'), $request->user())]);
                }
                foreach ($rules as $field => $rules) {
                    if ('avatar' == $field) {
                        if (strlen($request->avatar) > 0) {
                            $request->user()->{$field} = $request->input($field);
                        }
                    } else {
                        $request->user()->{$field} = $request->input($field);
                    }
                }
                $request->user()->save();
                return Redirect::route('my-preferences')->with('globalsuccessmessage', __('Updated My Preferences Successfully'));
                break;

            case 'security':
                Validator::make($request->all(), [
                    'password' => 'string|nullable|confirmed',
                    'password_confirmation' => 'string|nullable|required_with:password',
                    'google2fa_secret' => 'string|nullable|googlemfasecret',
                ])->validate();
                $request->user()->google2fa_secret = $request->input('google2fa_secret');
                if ($request->has('password')) {
                    $request->user()->password = Hash::make($request->input('password'));
                }
                $request->user()->save();
                return Redirect::route('my-preferences')->with('globalsuccessmessage', __('Updated My Security Settings Successfully'));
                break;
            
            default:
                return Redirect::route('my-preferences')->with('globalerrormessage', sprintf(__('Unknown Section "%s"'), $section));
                break;
        }
    }

    public static function makeCalendardLink($year = null, $month = null, $date = null, $view = 'day')
    {
        $timezone = (!is_null(request()->user()->timezone)) ? request()->user()->timezone : config('app.timezone');
        $query = [];
        if (is_null($year)) {
            $year = date('Y');
        }
        if (is_null($month)) {
            $month = date('m');
        }
        if (is_null($date)) {
            $date = date('Y-m-d');
        }
        if (is_null($view)) {
            $view = 'day';
        }
        if (intval($year) !== intval(date('Y'))) {
            $query['year'] = intval($year);
        }
        if (intval($month) !== intval(date('m'))) {
            $query['month'] = intval($month);
        }
        if ('day' !== $view) {
            $query['view'] = $view;
        }
        $today = Carbon::today($timezone)->setTime(0, 0, 0);
        $today->setTime(0, 0, 0);
        $date = new Carbon($date);
        $date->setTime(0, 0, 0);
        if (!$today->equalTo($date)) {
            $query['date'] = $date->toDateTimeString();
        }
        return route('my-calendar', $query);
    }
}
