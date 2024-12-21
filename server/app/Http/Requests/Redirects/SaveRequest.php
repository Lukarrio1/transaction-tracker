<?php

namespace App\Http\Requests\Redirects;

use Illuminate\Foundation\Http\FormRequest;

class SaveRequest extends FormRequest
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
    *
    * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
    */

    public function rules(): array
    {
        return [
            'role_id' => [ 'required' ],
            'redirect_to_after_login' => [ 'required' ],
            'redirect_to_after_register' => [ 'required' ],
            'redirect_to_after_logout' => [ 'required' ],
            // 'redirect_to_after_password_reset' => [ 'required' ],

        ];
    }
}
