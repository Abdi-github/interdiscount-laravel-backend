<?php

namespace App\Domain\Order\Listeners;

use App\Domain\Payment\Services\PaymentService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class ProcessOrderRefund implements ShouldQueue
{
    public string $queue = 'default';

    public function __construct(private PaymentService $paymentService) {}

    public function handle(object $event): void
    {
        $order = $event->order;

        Log::info('Processing refund for order', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'status' => $order->status,
        ]);

        $this->paymentService->refundPayment($order);
    }
}
