<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use \App\Role;

class RoleCannotBeItsOwnChild implements Rule
{
    protected $role;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(Role $role)
    {
        $this->role = $role;
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
        $childRoles = $this->role->getChildrenRoles()->pluck('id')->toArray();
        return !in_array($value, $childRoles);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('A Role cannot be a child of one of it\'s own children');
    }
}
