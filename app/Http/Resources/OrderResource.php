<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            '_id' => (string) $this->id,
            'order_number' => $this->order_number,
            'user_id' => $this->user_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'shipping_address_id' => $this->shipping_address_id,
            'shipping_address' => new AddressResource($this->whenLoaded('shippingAddress')),
            'billing_address_id' => $this->billing_address_id,
            'billing_address' => new AddressResource($this->whenLoaded('billingAddress')),
            'status' => $this->status?->value,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status?->value,
            'subtotal' => (float) $this->subtotal,
            'shipping_fee' => (float) $this->shipping_fee,
            'discount' => (float) $this->discount,
            'total' => (float) $this->total,
            'currency' => $this->currency,
            'coupon_code' => $this->coupon_code,
            'notes' => $this->notes,
            'store_pickup_id' => $this->store_pickup_id,
            'store_pickup' => new StoreResource($this->whenLoaded('storePickup')),
            'is_store_pickup' => $this->is_store_pickup,
            'estimated_delivery' => $this->estimated_delivery?->toDateString(),
            'delivered_at' => $this->delivered_at?->toISOString(),
            'cancelled_at' => $this->cancelled_at?->toISOString(),
            'cancellation_reason' => $this->cancellation_reason,
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
