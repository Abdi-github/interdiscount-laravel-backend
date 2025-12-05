<?php

namespace App\Http\Requests\Location;

use Illuminate\Foundation\Http\FormRequest;

class StoreCityRequest extends FormRequest
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
            'canton_id' => ['required', 'exists:cantons,id'],
            'postal_codes' => ['required', 'array', 'min:1'],
            'postal_codes.*' => ['string', 'max:10'],
            'slug' => ['required', 'string', 'max:100', 'unique:cities,slug'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
