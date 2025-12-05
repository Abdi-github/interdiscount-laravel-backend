<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockTransferResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            '_id' => (string) $this->id,
            'transfer_number' => $this->transfer_number,
            'from_store_id' => $this->from_store_id,
            'from_store' => new StoreResource($this->whenLoaded('fromStore')),
            'to_store_id' => $this->to_store_id,
            'to_store' => new StoreResource($this->whenLoaded('toStore')),
            'initiated_by' => $this->initiated_by,
            'status' => $this->status?->value,
            'items' => $this->items,
            'notes' => $this->notes,
            'approved_by' => $this->approved_by,
            'shipped_at' => $this->shipped_at?->toISOString(),
            'received_at' => $this->received_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
