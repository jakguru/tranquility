<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;

class Google2FA implements Rule
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
        $user = request()->user();
        if (request()->session()->has('google2fa_secret')) {
            $secret = request()->session()->get('google2fa_secret');
        } else {
            $secret = (is_a($user, '\App\User')) ? $user->google2fa_secret : null;
        }
        $google2fa = app('pragmarx.google2fa');
        try {
            $ret = $google2fa->verify($value, $secret);
        } catch (\Exception $e) {
            $ret = false;
        }
        if (false == $ret) {
            request()->session()->flash('google2fa_secret', $secret);
        }
        return $ret;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The Google Authenticator code is invalid.';
    }
}
