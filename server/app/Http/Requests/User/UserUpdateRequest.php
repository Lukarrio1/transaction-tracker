<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
    //  bug to fix
        // User::find((int) $this->id)->update($this->except(['password'] + ['password' => Hash::make($this->password)]));
return [];
        // return [
        //     'name'=>['required','min:3'],
        //     'email'=>['required','email','unique:users,email,'.$this->id],
        //     'password'=>['same:confirm_password','min:6'],
        //     'confirm_password'=>['same:password','min:6']
        // ];
    }

}
