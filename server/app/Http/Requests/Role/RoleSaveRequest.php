<?php

namespace App\Http\Requests\Role;

use App\Models\Setting;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Http\FormRequest;

class RoleSaveRequest extends FormRequest
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

        $setting = \optional(Setting::where('key', 'admin_role')->first())->getSettingValue();
        $role = !empty($setting) ? Role::find((int)$setting) : null;
        $highest_priority = !\request()->user()->hasRole($role) ? ['not_in:' . Role::min('priority'), 'required'] : ['required'];

        return [
            'name' => ['required', 'unique:roles,name,' . $this->id,],
            'priority' => $highest_priority
        ];
    }
}
