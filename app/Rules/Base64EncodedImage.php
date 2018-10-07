<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Base64EncodedImage implements Rule
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
        if (false !== strpos($value, ',')) {
            list($encoding, $image) = explode(',', $value);
            $img = @imagecreatefromstring(base64_decode($image));
            if (!$img) {
                return false;
            }
            imagepng($img, 'tmp.png');
            $info = getimagesize('tmp.png');
            unlink('tmp.png');
            return ($info[0] > 0 && $info[1] > 0 && $info['mime']);
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid Avatar Image';
    }
}
