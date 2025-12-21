<?php

namespace App\Domain\Transfer\Events;

use App\Domain\Transfer\Models\StockTransfer;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransferApproved
{
    use Dispatchable, SerializesModels;

    public function __construct(public StockTransfer $transfer) {}
}
