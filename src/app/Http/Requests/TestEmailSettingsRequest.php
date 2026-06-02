<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TestEmailSettingsRequest extends FormRequest
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
        return [
            'mail_mailers_smtp_host' => 'required|string',
            'mail_mailers_smtp_port' => 'required|integer',
            'mail_mailers_smtp_username' => 'nullable|string',
            'mail_mailers_smtp_password' => 'required|string',
            'mail_mailers_smtp_encryption' => 'required|string',
        ];
    }
}
