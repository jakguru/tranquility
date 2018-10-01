<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list(Request $request)
    {
        if (!$request->user()->can('list', User::class)) {
            abort(403);
        }
        $listHelper = new \App\Helpers\ModelListHelper(User::class, $request);
        if ($request->wantsJson()) {
            return \App\Helpers\AjaxFeedbackHelper::success($listHelper->getAJAXReturn(), 'Generated List Successfully');
        }
        return view('app.layouts.modellist', $listHelper->getViewVariables());
    }

    public function view(Request $request, $id)
    {
        $user = User::find($id);
        if (!is_a($user, '\App\User')) {
            abort(404);
        }
        if (!$request->user()->can('view', $user)) {
            abort(403);
        }
        return view('app.layouts.models.users.view', ['model' => $user]);
    }

    public function edit(Request $request, $id)
    {
        $user = User::find($id);
        if (!is_a($user, '\App\User')) {
            abort(404);
        }
        if (!$request->user()->can('edit', $user)) {
            abort(403);
        }
        return view('app.layouts.models.users.edit', ['model' => $user]);
    }

    public function audit(Request $request, $id)
    {
        $user = User::find($id);
        if (!is_a($user, '\App\User')) {
            abort(404);
        }
        if (!$request->user()->can('view', $user)) {
            abort(403);
        }
        $total_count = $user->activities()->count();
        $page = intval($request->input('page', 1));
        if ($page < 1) {
            $page = 1;
        }
        $total_pages = (ceil($total_count / config('app.listsize')));
        return view('app.layouts.models.shared.audit', [
            'model' => $user,
            'total_activities' => $total_count,
            'page' => $page,
            'total_pages' => $total_pages,
            'activities' => $user->activities()->limit(config('app.listsize'))->offset(($page - 1) * config('app.listsize'))->latest()->get(),
            'next_page' => ($page < ceil($total_count / config('app.listsize')) ) ? $page + 1 : 0,
            'previous_page' => ($page > 1) ? $page - 1 : 0,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!is_a($user, '\App\User')) {
            abort(404);
        }
        if (!$request->user()->can('edit', $user)) {
            abort(403);
        }
        $section = $request->input('section');
        switch ($section) {
            case 'personal':
                $updatable = [
                    'title',
                    'fName',
                    'lName',
                    'email',
                    'main_phone_country',
                    'main_phone',
                    'mobile_phone_country',
                    'mobile_phone',
                    'home_phone_country',
                    'home_phone',
                    'work_phone_country',
                    'work_phone',
                    'fax_phone_country',
                    'fax_phone',
                    'birthday',
                    'address_line_1',
                    'address_line_2',
                    'city',
                    'state',
                    'postal',
                    'country',
                    'whatsapp_phone_country',
                    'whatsapp_phone',
                    'telegram_phone_country',
                    'telegram_phone',
                    'viber_phone_country',
                    'viber_phone',
                    'skype',
                    'facebook',
                    'googleplus',
                    'linkedin'
                ];
                foreach ($updatable as $key) {
                    if (ends_with($key, '_phone')) {
                        $val = $request->input($key);
                        if (is_null($val) || 0 == strlen($val)) {
                            $countryKey = sprintf('%s_country', $key);
                            $request->merge([$countryKey => '']);
                        }
                    }
                }
                Validator::make($request->all(), [
                    'title' => 'string|nullable',
                    'fName' => 'required|string',
                    'lName' => 'required|string',
                    'email' => [
                        'required',
                        'email',
                        Rule::unique('users')->ignore($user->id),
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
                    'country' => ['string', 'nullable', Rule::in(\App\Helpers\CountryHelper::getCountriesForValidation(false))],
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
                ])->validate();
                foreach ($updatable as $key) {
                    switch (true) {
                        case ends_with($key, '_phone'):
                            $countryKey = sprintf('%s_country', $key);
                            $country = $request->input($countryKey);
                            $phone = new \App\Helpers\PhoneHelper($request->input($key), $country);
                            $user->{$key} = $phone->format();
                            break;

                        case ('email' == $key):
                            $user->{$key} = strtolower($request->input($key));
                            break;

                        case ends_with($key, 'Name'):
                            $user->{$key} = ucwords($request->input($key));
                            break;
                        
                        default:
                            $user->{$key} = $request->input($key);
                            break;
                    }
                }
                $user->save();
                return Redirect::route('view-user', ['id' => $user->id])->with('globalsuccessmessage', __('Updated Personal Information Successfully'));
                break;
            
            default:
                return Redirect::route('edit-user', ['id' => $user->id])->with('globalerrormessage', sprintf(__('Unknown Section "%s"'), $section));
                break;
        }
    }
}
