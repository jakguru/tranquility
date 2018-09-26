<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class GoogleReCAPCHA implements Rule
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
        return \App\Helpers\GoogleReCAPCHAHelper::validate($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'You must prove that you are not a robot.';
    }
}
