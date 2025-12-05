<?php

namespace App\Http\Requests\Transfer;

use Illuminate\Foundation\Http\FormRequest;

class ReceiveTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => 'sometimes|array',
            'items.*.product_id' => 'required|integer',
            'items.*.received_quantity' => 'required|integer|min:0',
        ];
    }
}
