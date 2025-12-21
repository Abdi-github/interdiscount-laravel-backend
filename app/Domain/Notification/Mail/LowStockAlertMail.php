<?php

namespace App\Domain\Notification\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LowStockAlertMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $productName;
    public string $productCode;
    public string $storeName;
    public int $currentQuantity;
    public int $minStock;

    public function __construct(
        string $productName,
        string $productCode,
        string $storeName,
        int $currentQuantity,
        int $minStock,
    ) {
        $this->productName = $productName;
        $this->productCode = $productCode;
        $this->storeName = $storeName;
        $this->currentQuantity = $currentQuantity;
        $this->minStock = $minStock;
        $this->queue = 'emails';
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Low Stock Alert: {$this->productName} at {$this->storeName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.low-stock-alert',
        );
    }
}
