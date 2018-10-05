<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

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
        abort(501);
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
        			'avatar' => 'nullable|string',
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
        		echo '<pre>';
        		print_r($request->all());
        		echo '</pre>';
        		exit();
        		break;

        	case 'security':
        		echo '<pre>';
        		print_r($request->all());
        		echo '</pre>';
        		exit();
        		break;
        	
        	default:
        		return Redirect::route('my-preferences')->with('globalerrormessage', sprintf(__('Unknown Section "%s"'), $section));
        		break;
        }
    }
}
