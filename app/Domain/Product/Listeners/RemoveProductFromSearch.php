<?php

namespace App\Domain\Product\Listeners;

use App\Domain\Product\Events\ProductStatusChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class RemoveProductFromSearch implements ShouldQueue
{
    public string $queue = 'search';

    public function handle(ProductStatusChanged $event): void
    {
        if ($event->newStatus !== 'active') {
            $event->product->unsearchable();
            Log::info('Product removed from search', ['product_id' => $event->product->id]);
        }
    }
}
