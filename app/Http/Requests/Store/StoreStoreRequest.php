<?php

namespace App\Http\Requests\Store;

use Illuminate\Foundation\Http\FormRequest;

class StoreStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:stores,slug'],
            'store_id' => ['required', 'string', 'max:50', 'unique:stores,store_id'],
            'street' => ['required', 'string', 'max:255'],
            'street_number' => ['nullable', 'string', 'max:50'],
            'postal_code' => ['required', 'string', 'max:10'],
            'city_id' => ['required', 'exists:cities,id'],
            'canton_id' => ['required', 'exists:cantons,id'],
            'remarks' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'format' => ['nullable', 'string', 'max:50'],
            'is_xxl' => ['nullable', 'boolean'],
            'opening_hours' => ['nullable', 'array'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
