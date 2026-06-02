<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GeneralInfoRequest extends FormRequest
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
        return [
            'name' => 'required|string',
            'owner' => 'required|string',
            'additional_address' => 'nullable|string',
            'street' => 'required|string',
            'postal' => 'required|string',
            'city' => 'required|string',
            'country' => 'required|string',
            'fax' => 'nullable|string',
            'email' => 'required|email',
            'homepage' => 'required|url',
        ];
    }
}
