<?php

namespace App\Domain\Product\Events;

use App\Domain\Product\Models\Product;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Product $product,
        public string $oldStatus,
        public string $newStatus,
    ) {}
}
