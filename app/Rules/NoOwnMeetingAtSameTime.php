<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use \App\Meeting;

class NoOwnMeetingAtSameTime implements Rule
{
    protected $user;
    protected $start;
    protected $end;
    protected $conflicts = [];

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $timezone = (!is_null(request()->user()->timezone)) ? request()->user()->timezone : config('app.timezone');
        $this->user = $request->user();
        $this->start = \Carbon\Carbon::parse($request->input('from'), $timezone)->setTimezone('UTC');
        $this->end = \Carbon\Carbon::parse($request->input('to'), $timezone)->setTimezone('UTC');
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $ownMeetingsQuery = $this->user->ownMeetings();
        \App\Helpers\RangeWithinRangeQueryHelper::modifyQuery($ownMeetingsQuery, $this->start, $this->end);
        $ownMeetings = $ownMeetingsQuery->get();
        self::log(print_r($ownMeetings->toArray(), true));
        foreach ($ownMeetings as $meeting) {
            $owner = $meeting->owner;
            if ($this->user->id == $owner->id) {
                array_push($this->conflicts, $meeting->id);
            }
        }
        $participatingMeetingsQuery = $this->user->meetings();
        \App\Helpers\RangeWithinRangeQueryHelper::modifyQuery($participatingMeetingsQuery, $this->start, $this->end);
        $participatingMeetings = $participatingMeetingsQuery->get();
        self::log(print_r($participatingMeetings->toArray(), true));
        foreach ($participatingMeetings as $meeting) {
            if ($this->user->id == $meeting->pivot->participant_id && $model == '\\App\\User') {
                array_push($this->conflicts, $meeting->id);
            }
        }
        return (count($this->conflicts) == 0);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'You already have an appointment scheduled at this time.';
    }

    protected static function log($what, $type = 'info', $force = false)
    {
        if (true == config('app.debug') || true == $force) {
            forward_static_call(['Log', $type], $what);
        }
    }
}
