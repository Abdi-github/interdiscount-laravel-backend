<?php

namespace App\Http\Requests\Store;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $storeId = $this->route('id');

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255', Rule::unique('stores', 'slug')->ignore($storeId)],
            'store_id' => ['sometimes', 'string', 'max:50', Rule::unique('stores', 'store_id')->ignore($storeId)],
            'street' => ['sometimes', 'string', 'max:255'],
            'street_number' => ['nullable', 'string', 'max:50'],
            'postal_code' => ['sometimes', 'string', 'max:10'],
            'city_id' => ['sometimes', 'exists:cities,id'],
            'canton_id' => ['sometimes', 'exists:cantons,id'],
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
