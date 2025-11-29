<?php

namespace App\Domain\Inventory\Events;

use App\Domain\Inventory\Models\StoreInventory;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LowStockDetected
{
    use Dispatchable, SerializesModels;

    public function __construct(public StoreInventory $inventory) {}
}
