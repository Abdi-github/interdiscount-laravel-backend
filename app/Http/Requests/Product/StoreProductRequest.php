<?php

namespace App\Http\Requests\Product;

use App\Domain\Product\Enums\AvailabilityState;
use App\Domain\Product\Enums\ProductStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:500'],
            'name_short' => ['nullable', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:500', 'unique:products,slug'],
            'code' => ['required', 'string', 'max:100', 'unique:products,code'],
            'displayed_code' => ['nullable', 'string', 'max:100'],
            'brand_id' => ['required', 'exists:brands,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'original_price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:3'],
            'images' => ['nullable', 'array'],
            'specification' => ['nullable', 'string'],
            'availability_state' => ['nullable', Rule::enum(AvailabilityState::class)],
            'delivery_days' => ['nullable', 'integer', 'min:0'],
            'in_store_possible' => ['nullable', 'boolean'],
            'release_date' => ['nullable', 'date'],
            'services' => ['nullable', 'array'],
            'promo_labels' => ['nullable', 'array'],
            'is_speed_product' => ['nullable', 'boolean'],
            'is_orderable' => ['nullable', 'boolean'],
            'is_sustainable' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'status' => ['nullable', Rule::enum(ProductStatus::class)],
        ];
    }
}
