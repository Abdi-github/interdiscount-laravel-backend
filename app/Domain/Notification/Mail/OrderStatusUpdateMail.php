<?php

namespace App\Domain\Notification\Mail;

use App\Domain\Order\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdateMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $userName;
    public string $orderNumber;
    public string $status;
    public string $cancellationReason;

    public function __construct(Order $order)
    {
        $order->loadMissing('user');

        $this->userName = $order->user->first_name;
        $this->orderNumber = $order->order_number;
        $this->status = $order->status->value;
        $this->cancellationReason = $order->cancellation_reason ?? '';
        $this->queue = 'emails';
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Order #{$this->orderNumber} - Status Update - Interdiscount",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-status',
        );
    }
}
