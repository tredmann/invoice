<?php

namespace App\Rules;

use App\Models\Tenant\Tenant;
use Illuminate\Contracts\Validation\Rule;

class NotInTenant implements Rule
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
        return app(Tenant::class)
            ->users()
            ->where('email', '=', $value)
            ->first() === null;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'This user is already in this tenant.';
    }
}
