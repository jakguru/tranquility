<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use \App\Helpers\AjaxFeedbackHelper;

class MyController extends Controller
{

    use \App\Helpers\DebugLoggable;


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
        if (!$request->user()->can('list', \App\Meeting::class)) {
            abort(403);
        }
        $timezone = (!is_null($request->user()->timezone)) ? $request->user()->timezone : config('app.timezone');
        $params = new \stdClass();
        $params->view = 'day';
        $params->year = intval($request->input('year', date('Y')));
        $params->month = intval($request->input('month', date('m')));
        $params->date = new Carbon($request->input('date', date('Y-m-d')), $timezone);
        $params->cmonth = Carbon::createFromDate($params->year, $params->month);
        $params->items = [];
        $params->timezone = $timezone;
        $params->showChildren = ($request->has('showChildren') && true == $request->input('showChildren'));
        $days = array_keys(\App\Http\Controllers\SettingsController::getListOfDays());
        $first_day = null;
        $params->days = [];
        $myAppointmentStart = $params->date->copy()->setTime(0, 0, 0)->setTimezone('UTC');
        $myAppointmentEnd = $params->date->copy()->setTime(23, 59, 59)->setTimezone('UTC');
        $params->myappointmentstoday = self::getMyAppointmentsBetweenDates($myAppointmentStart, $myAppointmentEnd);
        $params->appointment = self::getMyAppointmentsBetweenDates($myAppointmentStart, $myAppointmentEnd, $params->showChildren, true);
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

    public function createAppointment(Request $request)
    {
        $timezone = (!is_null(request()->user()->timezone)) ? request()->user()->timezone : config('app.timezone');
        if (!$request->user()->can('add', \App\Meeting::class)) {
            return AjaxFeedbackHelper::failureReponse(null, __('You are not allowed to create an appointment.'), 200, [__('You are not allowed to create an appointment.')]);
        }
        $rules = [
            'subject' => ['required', 'string', new \App\Rules\NoOwnMeetingAtSameTime($request)],
            'from' => 'required|date|after_or_equal:now',
            'to' => 'required|date|after:from',
            'description' => 'nullable|string',
            'participants' => ['nullable', 'array', new \App\Rules\ValidParticipant('participant'), new \App\Rules\NotSelf, new \App\Rules\NoMeetingAtSameTime($request)],
        ];
        $validator = Validator::make($request->all(), $rules);
        $errors = $validator->errors()->toArray();
        if (count($errors) > 0) {
            $returnerrors = [];
            foreach ($errors as $field => $err) {
                if (is_array($err)) {
                    foreach ($err as $e) {
                        array_push($returnerrors, $e);
                    }
                } else {
                    array_push($returnerrors, $err);
                }
            }
            return AjaxFeedbackHelper::failureReponse(null, __('Your form has errors'), 200, $returnerrors);
        }
        $meeting = new \App\Meeting;
        $meeting->subject = $request->input('subject');
        $meeting->starts_at = \Carbon\Carbon::parse($request->input('from'), $timezone)->setTimezone('UTC');
        $meeting->ends_at = \Carbon\Carbon::parse($request->input('to'), $timezone)->setTimezone('UTC');
        $meeting->description = $request->input('description');
        $meeting->owner_id = $request->user()->id;
        $emails = [];
        $meeting->email_participants = $emails;
        $meeting->save();
        $users = [];
        if (is_array($request->input('participants'))) {
            foreach ($request->input('participants') as $rawchoice) {
                $choice = @json_decode($rawchoice);
                if (!is_object($choice)) {
                    array_push($this->invalid, $choice->value);
                }
                $receivable = \App\Helpers\PermissionsHelper::getModelsWithTrait('Receivable');
                if ('email' == $choice->type) {
                    array_push($emails, $choice->value);
                } else {
                    $model = sprintf('\\App\\%s', ucfirst($choice->type));
                    $obj = $model::find($choice->value);
                    $property = \App\Helpers\ModelListHelper::getPluralLabelForClass($model);
                    $relationship = call_user_func(array($meeting, $property));
                    $relationship->attach($obj, ['status' => 'pending']);
                    if ('\\App\\User' == $model) {
                        array_push($users, $obj);
                    }
                }
            }
            $meeting->email_participants = $emails;
        }
        $meeting->save();
        $url = route('view-meeting', ['id' => $meeting->id]);
        foreach ($users as $user) {
            \App\Realtime\Events\RealtimeAlert::emitAlert(
                $user,
                sprintf(__('You have been invited to participate in a meeting with the subject "%s"'), $meeting->subject),
                $url,
                'info',
                'far fa-calendar-check'
            );
        }
        foreach ($emails as $email) {
            // @TODO: Send an email with an ical file with the details of the meeting
        }
        return AjaxFeedbackHelper::successResponse($url, __('Created Meeting Successfully'));
    }

    public function appointment(Request $request, $id)
    {
        $meeting = \App\Meeting::find($id);
        if (!is_a($meeting, '\App\Meeting')) {
            abort(404);
        }
        if (!$request->user()->can('view', $meeting)) {
            abort(403);
        }
        return view('app.layouts.models.meetings.view', [
            'model' => $meeting,
            'mymeeting' => ($request->user()->id == $meeting->owner_id),
            'ongoing' => ('success' == self::getAppointmentDisplayClass($meeting)),
            'past' => ('light' == self::getAppointmentDisplayClass($meeting)),
        ]);
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
                foreach ($rules as $key => $rule) {
                    if (ends_with($key, '_phone')) {
                        $val = $request->input($key);
                        if (is_null($val) || 0 == strlen($val)) {
                            $countryKey = sprintf('%s_country', $key);
                            $request->merge([$countryKey => '']);
                        }
                    }
                    if ('boolean' == $rule) {
                        $request->merge([$key => $request->has($key)]);
                    }
                }
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

    public static function makeCalendardLink($year = null, $month = null, $date = null, $view = 'day', $showChildren = false)
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
        if (true === $showChildren) {
            $query['showChildren'] = true;
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

    public static function getMyAppointmentsBetweenDates($start = null, $end = null, $children = false, $pending = false)
    {
        $timezone = (!is_null(request()->user()->timezone)) ? request()->user()->timezone : config('app.timezone');
        if (!is_a($start, '\Carbon\Carbon')) {
            if (is_null($start)) {
                $start = Carbon::today($timezone)->setTime(0, 0, 0);
            } else {
                $start = Carbon::parse($start, $timezone);
            }
        }
        if (!is_a($end, '\Carbon\Carbon')) {
            if (is_null($end)) {
                $end = Carbon::today($timezone)->setTime(23, 59, 59);
            } else {
                $end = Carbon::parse($end, $timezone);
            }
        }
        $start->setTimezone('UTC');
        $end->setTimezone('UTC');
        $collection = [];
        if (true == $children) {
            $permission_level = request()->user()->getPermissionForVerb('Meeting', 'view');
            if ('all' == $permission_level) {
                $participatingMeetingsQuery = \App\Meeting::select('meetings.*');
                $ownMeetingsQuery = \App\Meeting::select('meetings.*');
            } else {
                $participatingMeetingsQuery = \App\Meeting::join('participants', 'meetings.id', '=', 'participants.meeting_id')->select('meetings.*');
                $participatingMeetingsQuery->whereIn('participants.participant_id', request()->user()->getOwnerIds())->where('participants.participant_type', 'App\User');
                $ownMeetingsQuery = \App\Meeting::whereIn('owner_id', request()->user()->getOwnerIds());
            }
            if (false == $pending) {
                $participatingMeetingsQuery->where('participants.status', 'accepted');
            }
        } else {
            $participatingMeetingsQuery = request()->user()->meetings();
            $ownMeetingsQuery = request()->user()->ownMeetings();
        }
        \App\Helpers\RangeWithinRangeQueryHelper::modifyQuery($ownMeetingsQuery, $start, $end);
        \App\Helpers\RangeWithinRangeQueryHelper::modifyQuery($participatingMeetingsQuery, $start, $end);
        if (false == $pending && false == $children) {
            $participatingMeetingsQuery->wherePivot('status', '=', 'accepted');
        }
        foreach ($ownMeetingsQuery->get() as $meeting) {
            array_push($collection, $meeting);
        }
        foreach ($participatingMeetingsQuery->get() as $meeting) {
            array_push($collection, $meeting);
        }
        return collect($collection)->unique('id')->sortBy('starts_at');
    }

    public static function getAppointmentDisplayClass(\App\Meeting $meeting, $prefix = '')
    {
        $now = Carbon::now();
        $return = '';
        if (Carbon::parse($meeting->ends_at)->lessThan($now)) {
            $return .= ' ' . $prefix . 'light';
        } elseif ($now->greaterThanOrEqualTo(Carbon::parse($meeting->starts_at)) && $now->lessThanOrEqualTo(Carbon::parse($meeting->ends_at))) {
            $return .= ' ' . $prefix . 'success';
        } elseif (request()->user()->id !== $meeting->owner_id) {
            $return .= ' ' . $prefix . 'warning';
        }
        return trim($return);
    }

    public static function meetingInTimeRange($dt, $start, $end)
    {
        $start = Carbon::parse($start)->addSecond();
        $end = Carbon::parse($end)->subSecond();
        $re = $dt->copy()->addMinutes(15);
        if ($start->between($dt, $re) || $end->between($dt, $re) || ($start->lte($dt) && $end->gte($re))) {
            return true;
        }
        return false;
    }
}
