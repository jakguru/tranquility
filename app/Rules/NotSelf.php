<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use \Auth;

class NotSelf implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        foreach ($value as $rawchoice) {
            $choice = @json_decode($rawchoice);
            if (!is_object($choice)) {
                array_push($this->invalid, $choice);
            }
            $receivable = \App\Helpers\PermissionsHelper::getModelsWithTrait('Receivable');
            if ('email' == $choice->type) {
                if (Auth::user()->email == $choice->value) {
                    return false;
                }
            } elseif ('user' == $choice->type) {
                $model = sprintf('\\App\\%s', ucfirst($choice->type));
                if (!in_array($model, $receivable)) {
                    return false;
                }
                // if we're dealing with a user, make sure it isn't ourselves
                $obj = $model::find($choice->value);
                if (!is_a($obj, $model) || is_null($obj->id)) {
                    return false;
                }
                if ($obj->id == Auth::user()->id) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('You cannot invite yourself as you are the organizer of this appointment.');
    }

    protected static function log($what, $type = 'info', $force = false)
    {
        if (true == config('app.debug') || true == $force) {
            forward_static_call(['Log', $type], $what);
        }
    }
}
