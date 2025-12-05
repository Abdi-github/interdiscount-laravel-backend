<?php

namespace App\Http\Requests\RBAC;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', 'unique:roles,name'],
            'display_name' => ['required', 'array'],
            'display_name.de' => ['required', 'string', 'max:100'],
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
