<?php

namespace App\Http\Requests\Location;

use Illuminate\Foundation\Http\FormRequest;

class StoreCantonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'array'],
            'name.de' => ['required', 'string', 'max:100'],
            'name.en' => ['nullable', 'string', 'max:100'],
            'name.fr' => ['nullable', 'string', 'max:100'],
            'name.it' => ['nullable', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:5', 'unique:cantons,code'],
            'slug' => ['required', 'string', 'max:100', 'unique:cantons,slug'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
