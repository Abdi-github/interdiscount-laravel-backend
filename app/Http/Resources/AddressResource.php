<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            '_id' => (string) $this->id,
            'user_id' => $this->user_id,
            'label' => $this->label,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'street' => $this->street,
            'street_number' => $this->street_number,
            'postal_code' => $this->postal_code,
            'city' => $this->city,
            'canton_code' => $this->canton_code,
            'country' => $this->country,
            'phone' => $this->phone,
            'is_default' => $this->is_default,
            'is_billing' => $this->is_billing,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
