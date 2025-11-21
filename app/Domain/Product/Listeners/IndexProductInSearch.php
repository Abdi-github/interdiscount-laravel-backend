<?php

namespace App\Domain\Product\Listeners;

use App\Domain\Product\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class IndexProductInSearch implements ShouldQueue
{
    public string $queue = 'search';

    public function handle(object $event): void
    {
        /** @var Product $product */
        $product = $event->product;

        if ($product->is_active && $product->status === 'active') {
            $product->searchable();
            Log::info('Product indexed in search', ['product_id' => $product->id]);
        } else {
            $product->unsearchable();
            Log::info('Product removed from search (inactive)', ['product_id' => $product->id]);
        }
    }
}
