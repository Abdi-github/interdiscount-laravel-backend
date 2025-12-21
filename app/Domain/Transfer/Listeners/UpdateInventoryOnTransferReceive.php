<?php

namespace App\Domain\Transfer\Listeners;

use App\Domain\Inventory\Models\StoreInventory;
use App\Domain\Transfer\Events\TransferReceived;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class UpdateInventoryOnTransferReceive implements ShouldQueue
{
    public string $queue = 'default';

    public function handle(TransferReceived $event): void
    {
        $transfer = $event->transfer;
        $items = $transfer->items ?? [];

        foreach ($items as $item) {
            $receivedQty = $item['received_quantity'] ?? $item['quantity'];

            $inventory = StoreInventory::firstOrCreate(
                [
                    'store_id' => $transfer->to_store_id,
                    'product_id' => $item['product_id'],
                ],
                [
                    'quantity' => 0,
                    'reserved' => 0,
                    'min_stock' => 5,
                    'max_stock' => 100,
                    'is_active' => true,
                ],
            );

            $inventory->increment('quantity', $receivedQty);
            $inventory->update(['last_restock_at' => now()]);

            Log::info('Inventory incremented on transfer receive', [
                'store_id' => $transfer->to_store_id,
                'product_id' => $item['product_id'],
                'quantity' => $receivedQty,
            ]);
        }
    }
}
