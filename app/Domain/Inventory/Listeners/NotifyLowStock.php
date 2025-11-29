<?php

namespace App\Domain\Inventory\Listeners;

use App\Domain\Inventory\Events\LowStockDetected;
use App\Domain\Notification\Mail\LowStockAlertMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyLowStock implements ShouldQueue
{
    public string $queue = 'emails';

    public function handle(LowStockDetected $event): void
    {
        $inventory = $event->inventory;
        $inventory->loadMissing(['store', 'product']);

        $store = $inventory->store;
        $product = $inventory->product;

        Log::warning('Low stock detected', [
            'store_id' => $store->id,
            'product_id' => $product->id,
            'quantity' => $inventory->quantity,
            'min_stock' => $inventory->min_stock,
        ]);

        // Send alert to store email
        if ($store->email) {
            Mail::to($store->email)->send(new LowStockAlertMail(
                product: $product,
                store: $store,
                quantity: $inventory->quantity,
            ));
        }
    }
}
