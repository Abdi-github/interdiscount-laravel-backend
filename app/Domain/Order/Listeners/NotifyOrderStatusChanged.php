<?php

namespace App\Domain\Order\Listeners;

use App\Domain\Notification\Mail\OrderStatusUpdateMail;
use App\Domain\Notification\Repositories\NotificationRepositoryInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class NotifyOrderStatusChanged implements ShouldQueue
{
    public string $queue = 'emails';

    public function __construct(
        private NotificationRepositoryInterface $notificationRepository,
    ) {}

    public function handle(object $event): void
    {
        $order = $event->order;
        $order->loadMissing('user');
        $status = $order->status->value;

        $this->notificationRepository->create([
            'user_id' => $order->user_id,
            'type' => 'order_status_changed',
            'title' => 'Order Status Updated',
            'message' => "Your order #{$order->order_number} status has been updated to {$status}.",
            'data' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $status,
            ],
        ]);

        Mail::to($order->user->email)->send(new OrderStatusUpdateMail($order));
    }
}
