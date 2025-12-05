<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quantity' => 'sometimes|integer|min:0',
            'reserved' => 'sometimes|integer|min:0',
            'min_stock' => 'sometimes|integer|min:0',
            'max_stock' => 'sometimes|integer|min:0',
            'location_in_store' => 'sometimes|nullable|string|max:255',
            'is_display_unit' => 'sometimes|boolean',
        ];
    }
}
