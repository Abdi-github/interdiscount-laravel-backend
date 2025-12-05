<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreInventoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            '_id' => (string) $this->id,
            'store_id' => $this->store_id,
            'store' => new StoreResource($this->whenLoaded('store')),
            'product_id' => $this->product_id,
            'product' => new ProductResource($this->whenLoaded('product')),
            'quantity' => $this->quantity,
            'reserved' => $this->reserved,
            'available' => $this->availableQuantity(),
            'min_stock' => $this->min_stock,
            'max_stock' => $this->max_stock,
            'last_restock_at' => $this->last_restock_at?->toISOString(),
            'last_sold_at' => $this->last_sold_at?->toISOString(),
            'location_in_store' => $this->location_in_store,
            'is_display_unit' => $this->is_display_unit,
            'is_active' => $this->is_active,
            'is_low_stock' => $this->isLowStock(),
            'is_out_of_stock' => $this->isOutOfStock(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
