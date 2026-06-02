<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Ownership implements Rule
{
    private $model;

    /**
     * Create a new rule instance.
     *
     * @param  string | object  $model
     */
    public function __construct($model)
    {
        $this->model = $model;
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
        $object = is_object($value) ? $value : (new $this->model())->findOrFail($value);

        return $object->user_id === auth()->id();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'This object does not belong to you.';
    }
}
