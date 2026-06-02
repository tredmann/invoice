<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
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
        return [

            'company' => 'required|string|max:100',
            'name' => 'nullable|string|max:50',
            'street' => 'required|string|max:100',
            'postal' => 'required|string|max:5',
            'city' => 'required|string|max:100',
        ];
    }
}
