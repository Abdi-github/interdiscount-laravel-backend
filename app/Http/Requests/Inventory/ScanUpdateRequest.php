<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class ScanUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_code' => 'required|string',
            'quantity' => 'sometimes|integer|min:0',
            'location_in_store' => 'sometimes|nullable|string|max:255',
        ];
    }
}
