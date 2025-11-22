<?php

namespace App\Domain\Payment\Listeners;

use App\Domain\Notification\Mail\PaymentFailedMail;
use App\Domain\Notification\Repositories\NotificationRepositoryInterface;
use App\Domain\Payment\Events\PaymentFailed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class NotifyPaymentFailed implements ShouldQueue
{
    public string $queue = 'emails';

    public function __construct(
        private NotificationRepositoryInterface $notificationRepository,
    ) {}

    public function handle(PaymentFailed $event): void
    {
        $payment = $event->payment;
        $payment->loadMissing(['user', 'order']);

        $this->notificationRepository->create([
            'user_id' => $payment->user_id,
            'type' => 'payment_failed',
            'title' => 'Payment Failed',
            'message' => "Payment for your order failed. Reason: {$payment->failure_reason}",
            'data' => [
                'payment_id' => $payment->id,
                'order_id' => $payment->order_id,
            ],
        ]);

        Mail::to($payment->user->email)->send(new PaymentFailedMail($payment));
    }
}
