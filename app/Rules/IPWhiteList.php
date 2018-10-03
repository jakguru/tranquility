<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use \App\Helpers\Validators;

class IPWhiteList implements Rule
{
    protected $invalidIps = [];
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
        $rawList = explode("\n", $value);
        $rawList = array_map('trim', $rawList);
        $rawList = array_map('strtolower', $rawList);
        foreach ($rawList as $ipRaw) {
            if (!Validators::is_ip($ipRaw) && !Validators::is_cidr($ipRaw) && !in_array($ipRaw, ['all', 'any', 'none'])) {
                array_push($this->invalidIps, $ipRaw);
            }
        }
        return (0 == count($this->invalidIps));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $ipList = $this->invalidIps;
        $ipList = array_map(function ($value) {
            if (!Validators::is_ip($value) || !Validators::is_cidr($value)) {
                $value = sprintf('"%s"', $value);
            }
            return $value;
        }, $ipList);
        if (count($ipList) > 1) {
            $lastIp = array_pop($ipList);
            $ipText = sprintf('%s and %s', implode(', ', $ipList), $lastIp);
            $ipIsAre = __('are');
            $ipSingleOrPlural = __('valid IP Addresses or CIDRs');
        } else {
            $ipText = array_shift($ipList);
            $ipIsAre = __('is');
            $ipSingleOrPlural = __('a valid IP Address or CIDR');
        }
        return sprintf(
            __('%s %s not %s'),
            $ipText,
            $ipIsAre,
            $ipSingleOrPlural
        );
    }
}
