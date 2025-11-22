<?php

namespace App\Domain\Payment\Listeners;

use App\Domain\Order\Enums\PaymentStatus;
use App\Domain\Order\Repositories\OrderRepositoryInterface;
use App\Domain\Payment\Events\PaymentSucceeded;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateOrderPaymentStatus implements ShouldQueue
{
    public string $queue = 'default';

    public function __construct(
        private OrderRepositoryInterface $orderRepository,
    ) {}

    public function handle(PaymentSucceeded $event): void
    {
        $payment = $event->payment;

        $this->orderRepository->update($payment->order_id, [
            'payment_status' => PaymentStatus::PAID,
        ]);
    }
}
