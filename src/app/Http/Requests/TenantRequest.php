<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Tenant\Tenant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TenantRequest extends FormRequest
{
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
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'name' => [
                'required',
                'string',
                'max:22',
                'unique:tenants,name',
            ],
        ];

        if ($this->isMethod(self::METHOD_PATCH)) {
            $rules['name'] = Rule::unique('tenants')->ignore(app(Tenant::class));
        }

        return $rules;
    }
}
