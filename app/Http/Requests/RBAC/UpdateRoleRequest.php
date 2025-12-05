<?php

namespace App\Http\Requests\RBAC;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roleId = $this->route('id');

        return [
            'name' => ['sometimes', 'string', 'max:100', Rule::unique('roles', 'name')->ignore($roleId)],
            'display_name' => ['sometimes', 'array'],
            'display_name.de' => ['sometimes', 'string', 'max:100'],
            'display_name.en' => ['nullable', 'string', 'max:100'],
            'display_name.fr' => ['nullable', 'string', 'max:100'],
            'display_name.it' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'array'],
            'description.de' => ['nullable', 'string'],
            'description.en' => ['nullable', 'string'],
            'description.fr' => ['nullable', 'string'],
            'description.it' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
