<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use \App\Meeting;

class NoMeetingAtSameTime implements Rule
{
    protected $start;
    protected $end;
    protected $conflicts = [];
    protected $message = '';
    protected $display = '';
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $timezone = (!is_null(request()->user()->timezone)) ? request()->user()->timezone : config('app.timezone');
        $this->start = \Carbon\Carbon::parse($request->input('from'), $timezone)->setTimezone('UTC');
        $this->end = \Carbon\Carbon::parse($request->input('to'), $timezone)->setTimezone('UTC')->subSeconds(1);
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
        if (!is_array($value)) {
            return true;
        }
        foreach ($value as $rawchoice) {
            $choice = @json_decode($rawchoice);
            if (!is_object($choice)) {
                return false;
            }
            $receivable = \App\Helpers\PermissionsHelper::getModelsWithTrait('Receivable');
            $this->display = $choice->display;
            if ('email' == $choice->type) {
                $meetingsQuery = Meeting::whereNotNull('email_participants');
                \App\Helpers\RangeWithinRangeQueryHelper::modifyQuery($meetingsQuery, $this->start, $this->end);
                $meetings = $meetingsQuery->get();
                self::log(print_r($meetings->toArray(), true));
                foreach ($meetings as $meeting) {
                    foreach ($meeting->email_participants as $email) {
                        if ($choice->value == $email) {
                            array_push($this->conflicts, $meeting->id);
                        }
                    }
                }
            } else {
                $model = sprintf('\\App\\%s', ucfirst($choice->type));
                if (!in_array($model, $receivable)) {
                    return false;
                }
                $obj = $model::find($choice->value);
                if (!is_a($obj, $model) || is_null($obj->id)) {
                    return false;
                }
                $ownMeetingsQuery = $obj->ownMeetings();
                \App\Helpers\RangeWithinRangeQueryHelper::modifyQuery($ownMeetingsQuery, $this->start, $this->end);
                $ownMeetings = $ownMeetingsQuery->get();
                self::log(print_r($ownMeetings->toArray(), true));
                foreach ($ownMeetings as $meeting) {
                    $owner = $meeting->owner;
                    if ($obj->id == $owner->id) {
                        array_push($this->conflicts, $meeting->id);
                    }
                }
                $participatingMeetingsQuery = $obj->meetings();
                \App\Helpers\RangeWithinRangeQueryHelper::modifyQuery($participatingMeetingsQuery, $this->start, $this->end);
                $participatingMeetings = $participatingMeetingsQuery->get();
                self::log(print_r($participatingMeetings->toArray(), true));
                foreach ($participatingMeetings as $meeting) {
                    if ($obj->id == $meeting->pivot->participant_id && $model == '\\' . $meeting->pivot->participant_type) {
                        array_push($this->conflicts, $meeting->id);
                    }
                }
            }
        }
        if (count($this->conflicts) > 0) {
            if (1 == count($this->conflicts)) {
                $this->message = sprintf(__('%s has an appointment which conflicts with this appointment.'), $this->display);
            } else {
                $this->message = sprintf(__('%s has %d appointments which conflict with this appointment.'), $this->display, count($this->conflicts));
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
        return $this->message;
    }

    protected static function log($what, $type = 'info', $force = false)
    {
        if (true == config('app.debug') || true == $force) {
            forward_static_call(['Log', $type], $what);
        }
    }
}
