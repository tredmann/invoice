<?php

namespace App\Http\Requests;

use App\Models\CustomerMailReceiver;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerMailReceiverRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
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
            'gender' => 'nullable',
            'string',
            'in:' . implode(',', CustomerMailReceiver::GENDER),
            'first_name' => 'nullable|required_unless:gender,null|string|max:50',
            'last_name' => 'nullable|required_unless:name,null|string|max:50',
        ];

        if ($this->isMethod(self::METHOD_POST)) {
            $rules['customer_id'] = ['required', 'string', 'exists:customers,id'];
            $rules['email'] = 'required|email|max:50';
        }

        if ($this->isMethod(self::METHOD_PATCH)) {
            $rules['email'] = [
                'required',
                'email',
                'max:50',
                Rule::unique('customer_mail_receivers')->ignore($this->customerMailReceiver->id),
            ];
        }

        return $rules;
    }
}
