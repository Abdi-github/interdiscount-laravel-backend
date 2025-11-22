<?php

namespace App\Domain\Order\Listeners;

use App\Domain\Inventory\Models\StoreInventory;
use App\Domain\Order\Events\OrderPlaced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class UpdateInventoryOnOrder implements ShouldQueue
{
    public string $queue = 'default';

    public function handle(OrderPlaced $event): void
    {
        $order = $event->order;
        $order->loadMissing('items');

        if (!$order->is_store_pickup || !$order->store_pickup_id) {
            return;
        }

        foreach ($order->items as $item) {
            $inventory = StoreInventory::where('store_id', $order->store_pickup_id)
                ->where('product_id', $item->product_id)
                ->first();

            if ($inventory) {
                $inventory->increment('reserved', $item->quantity);

                Log::info('Inventory reserved for order', [
                    'order_id' => $order->id,
                    'store_id' => $order->store_pickup_id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                ]);
            }
        }
    }
}
