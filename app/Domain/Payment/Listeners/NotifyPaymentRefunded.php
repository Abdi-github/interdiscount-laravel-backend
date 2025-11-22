<?php

namespace App\Domain\Payment\Listeners;

use App\Domain\Notification\Mail\PaymentRefundedMail;
use App\Domain\Notification\Repositories\NotificationRepositoryInterface;
use App\Domain\Payment\Events\PaymentRefunded;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class NotifyPaymentRefunded implements ShouldQueue
{
    public string $queue = 'emails';

    public function __construct(
        private NotificationRepositoryInterface $notificationRepository,
    ) {}

    public function handle(PaymentRefunded $event): void
    {
        $payment = $event->payment;
        $payment->loadMissing(['user', 'order']);

        $this->notificationRepository->create([
            'user_id' => $payment->user_id,
            'type' => 'payment_refunded',
            'title' => 'Payment Refunded',
            'message' => "A refund of CHF {$payment->amount} has been processed for your order.",
            'data' => [
                'payment_id' => $payment->id,
                'order_id' => $payment->order_id,
                'amount' => (float) $payment->amount,
            ],
        ]);

        Mail::to($payment->user->email)->send(new PaymentRefundedMail($payment));
    }
}
