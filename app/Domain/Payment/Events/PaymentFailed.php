<?php

namespace App\Domain\Payment\Events;

use App\Domain\Payment\Models\Payment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentFailed
{
    use Dispatchable, SerializesModels;

    public function __construct(public Payment $payment) {}
}
