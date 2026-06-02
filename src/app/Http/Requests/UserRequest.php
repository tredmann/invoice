<?php

namespace App\Http\Requests;

use App\Actions\Fortify\PasswordValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Rules\Password;

class UserRequest extends FormRequest
{
    use PasswordValidationRules;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    #[\Override]
    protected function prepareForValidation()
    {
        if ($this->password === null) {
            $this->request->remove('password');
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', new Password(), 'confirmed'],
        ];

        if ($this->isMethod(self::METHOD_PATCH)) {
            $rules['email'] = ['required', 'email', 'max:255', Rule::unique('users')->ignore($this->user->id)];
            $rules['password'] = ['nullable', 'string', new Password(), 'confirmed'];
        }

        return $rules;
    }
}
