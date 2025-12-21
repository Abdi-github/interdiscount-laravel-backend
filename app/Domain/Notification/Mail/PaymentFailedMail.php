<?php

namespace App\Domain\Notification\Mail;

use App\Domain\Payment\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentFailedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $userName;
    public string $orderNumber;
    public float $amount;
    public string $currency;
    public string $failureReason;

    public function __construct(Payment $payment)
    {
        $payment->loadMissing(['user', 'order']);

        $this->userName = $payment->user->first_name;
        $this->orderNumber = $payment->order->order_number;
        $this->amount = (float) $payment->amount;
        $this->currency = $payment->currency;
        $this->failureReason = $payment->failure_reason ?? '';
        $this->queue = 'emails';
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Failed - Interdiscount',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-failed',
        );
    }
}
