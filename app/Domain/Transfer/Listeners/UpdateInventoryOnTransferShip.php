<?php

namespace App\Domain\Transfer\Listeners;

use App\Domain\Inventory\Models\StoreInventory;
use App\Domain\Transfer\Events\TransferShipped;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class UpdateInventoryOnTransferShip implements ShouldQueue
{
    public string $queue = 'default';

    public function handle(TransferShipped $event): void
    {
        $transfer = $event->transfer;
        $items = $transfer->items ?? [];

        foreach ($items as $item) {
            $inventory = StoreInventory::where('store_id', $transfer->from_store_id)
                ->where('product_id', $item['product_id'])
                ->first();

            if ($inventory) {
                $inventory->decrement('quantity', $item['quantity']);
                Log::info('Inventory decremented on transfer ship', [
                    'store_id' => $transfer->from_store_id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);
            }
        }
    }
}
