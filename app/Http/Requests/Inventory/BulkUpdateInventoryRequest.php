<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class BulkUpdateInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'sometimes|integer|min:0',
            'items.*.reserved' => 'sometimes|integer|min:0',
            'items.*.min_stock' => 'sometimes|integer|min:0',
            'items.*.max_stock' => 'sometimes|integer|min:0',
            'items.*.location_in_store' => 'sometimes|nullable|string|max:255',
        ];
    }
}
