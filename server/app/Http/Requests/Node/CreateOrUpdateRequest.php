<?php

namespace App\Http\Requests\Node;

use App\Models\Node\Node_Type;
use Illuminate\Foundation\Http\FormRequest;

class CreateOrUpdateRequest extends FormRequest
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
            'node_name' => 'required',
            'node_description' => 'required',
            'node_authentication_level'=>'required',
            'node_type'=>'required',
        ];
    }
}
