<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ValidParticipant implements Rule
{
    protected $invalid = [];
    protected $label = 'recipient';
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($label = 'recipient')
    {
        $this->label = $label;
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
                $rule = ['email' => 'email'];
                $validator = Validator::make(['email' => $choice->value], $rule);
                if (count($validator->errors()->toArray()) > 0) {
                    array_push($this->invalid, $choice->display);
                }
            } else {
                $model = sprintf('\\App\\%s', ucfirst($choice->type));
                if (!in_array($model, $receivable)) {
                    array_push($this->invalid, $choice->display);
                }
                $obj = $model::find($choice->value);
                if (!is_a($obj, $model) || is_null($obj->id)) {
                    array_push($this->invalid, $choice->display);
                }
            }
        }
        return (count($this->invalid) == 0);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if (count($this->invalid) > 1) {
            $lastInvalid = array_pop($this->invalid);
            $invalidText = sprintf('%s and %s', implode(', ', $this->invalid), $lastInvalid);
            $invalidIsAre = __('are');
            $invalidSingleOrPlural = sprintf(__('valid %s'), str_plural($this->label));
        } else {
            $invalidText = array_shift($this->invalid);
            $invalidIsAre = __('is');
            $invalidSingleOrPlural = sprintf(__('a valid %s'), str_singular($this->label));
        }
        return sprintf(
            __('%s %s not %s.'),
            $invalidText,
            $invalidIsAre,
            $invalidSingleOrPlural
        );
    }

    protected static function log($what, $type = 'info', $force = false)
    {
        if (true == config('app.debug') || true == $force) {
            forward_static_call(['Log', $type], $what);
        }
    }
}
