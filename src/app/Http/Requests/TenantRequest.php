<?php

namespace App\Http\Requests;

use App\Models\Tenant\Tenant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TenantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool|\Illuminate\Auth\Access\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
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
