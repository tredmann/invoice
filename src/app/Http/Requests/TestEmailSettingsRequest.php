<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TestEmailSettingsRequest extends FormRequest
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
            'mail_mailers_smtp_host' => 'required|string',
            'mail_mailers_smtp_port' => 'required|integer',
            'mail_mailers_smtp_username' => 'nullable|string',
            'mail_mailers_smtp_password' => 'required|string',
            'mail_mailers_smtp_encryption' => 'required|string',
        ];
    }
}
