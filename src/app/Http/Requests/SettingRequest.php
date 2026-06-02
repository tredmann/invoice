<?php

namespace App\Http\Requests;

use App\Models\Setting;
use App\Models\Tenant\Tenant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
            'value' => 'required|string',
            'type' => 'required|string|in:' . implode(',', Setting::VALUE_TYPES),
        ];

        if ($this->isMethod(self::METHOD_POST)) {
            $rules['key'] = [
                'required',
                'string',
                'unique:settings,key,NULL,id,tenant_id,' . app(Tenant::class)->id,
            ];
        }

        if ($this->isMethod(self::METHOD_PATCH)) {
            $setting = request()->route('setting');
            $id = null;

            if ($setting instanceof Setting) {
                $id = $setting->id;
            }

            $rules['key'] = [
                'required',
                'string',
                Rule::unique('settings')
                    ->where(fn ($query) => $query->where('tenant_id', app(Tenant::class)->id))
                    ->ignore($id, 'id'),
            ];
        }

        return $rules;
    }
}
