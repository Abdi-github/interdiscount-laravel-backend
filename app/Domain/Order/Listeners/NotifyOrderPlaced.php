<?php

namespace App\Domain\Order\Listeners;

use App\Domain\Notification\Mail\OrderConfirmationMail;
use App\Domain\Notification\Repositories\NotificationRepositoryInterface;
use App\Domain\Order\Events\OrderPlaced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class NotifyOrderPlaced implements ShouldQueue
{
    public string $queue = 'emails';

    public function __construct(
        private NotificationRepositoryInterface $notificationRepository,
    ) {}

    public function handle(OrderPlaced $event): void
    {
        $order = $event->order;
        $order->loadMissing('user');

        $this->notificationRepository->create([
            'user_id' => $order->user_id,
            'type' => 'order_placed',
            'title' => 'Order Placed',
            'message' => "Your order #{$order->order_number} has been placed successfully.",
            'data' => ['order_id' => $order->id, 'order_number' => $order->order_number],
        ]);

        Mail::to($order->user->email)->send(new OrderConfirmationMail($order));
    }
}
