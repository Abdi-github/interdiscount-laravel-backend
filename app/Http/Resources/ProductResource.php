<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            '_id' => (string) $this->id,
            'name' => $this->name,
            'name_short' => $this->name_short,
            'slug' => $this->slug,
            'code' => $this->code,
            'displayed_code' => $this->displayed_code,
            'brand_id' => $this->brand_id,
            'brand' => new BrandResource($this->whenLoaded('brand')),
            'category_id' => $this->category_id,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'price' => (float) $this->price,
            'original_price' => $this->original_price ? (float) $this->original_price : null,
            'currency' => $this->currency,
            'images' => $this->images,
            'rating' => $this->rating ? (float) $this->rating : null,
            'review_count' => $this->review_count,
            'specification' => $this->specification,
            'availability_state' => $this->availability_state?->value,
            'delivery_days' => $this->delivery_days,
            'in_store_possible' => $this->in_store_possible,
            'release_date' => $this->release_date?->toDateString(),
            'services' => $this->services,
            'promo_labels' => $this->promo_labels,
            'is_speed_product' => $this->is_speed_product,
            'is_orderable' => $this->is_orderable,
            'is_sustainable' => $this->is_sustainable,
            'is_active' => $this->is_active,
            'status' => $this->status?->value,
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
