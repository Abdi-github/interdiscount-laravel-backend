<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            '_id' => (string) $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'store_id' => $this->store_id,
            'street' => $this->street,
            'street_number' => $this->street_number,
            'postal_code' => $this->postal_code,
            'city_id' => $this->city_id,
            'city' => new CityResource($this->whenLoaded('city')),
            'canton_id' => $this->canton_id,
            'canton' => new CantonResource($this->whenLoaded('canton')),
            'remarks' => $this->remarks,
            'phone' => $this->phone,
            'email' => $this->email,
            'latitude' => $this->latitude ? (float) $this->latitude : null,
            'longitude' => $this->longitude ? (float) $this->longitude : null,
            'format' => $this->format,
            'is_xxl' => $this->is_xxl,
            'opening_hours' => $this->opening_hours,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
