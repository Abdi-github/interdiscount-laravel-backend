<?php

namespace App\Domain\Notification\Mail;

use App\Domain\Order\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $userName;
    public string $orderNumber;
    public string $orderDate;
    public string $paymentMethod;
    public array $items;
    public float $subtotal;
    public float $discount;
    public float $shippingFee;
    public float $total;
    public string $currency;

    public function __construct(Order $order)
    {
        $order->loadMissing(['user', 'orderItems']);

        $this->userName = $order->user->first_name;
        $this->orderNumber = $order->order_number;
        $this->orderDate = $order->created_at->format('d.m.Y H:i');
        $this->paymentMethod = $order->payment_method->value;
        $this->subtotal = (float) $order->subtotal;
        $this->discount = (float) $order->discount;
        $this->shippingFee = (float) $order->shipping_fee;
        $this->total = (float) $order->total;
        $this->currency = $order->currency;

        $this->items = $order->orderItems->map(fn ($item) => [
            'product_name' => $item->product_name,
            'quantity' => $item->quantity,
            'total_price' => (float) $item->total_price,
            'currency' => $item->currency,
        ])->toArray();

        $this->queue = 'emails';
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Order Confirmation #{$this->orderNumber} - Interdiscount",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-confirmation',
        );
    }
}
