<?php

namespace App\Domain\Notification\Mail;

use App\Domain\Transfer\Models\StockTransfer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TransferStatusUpdateMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $transferNumber;
    public string $fromStore;
    public string $toStore;
    public string $status;
    public array $items;

    public function __construct(StockTransfer $transfer)
    {
        $transfer->loadMissing(['fromStore', 'toStore']);

        $this->transferNumber = $transfer->transfer_number;
        $this->fromStore = $transfer->fromStore->name;
        $this->toStore = $transfer->toStore->name;
        $this->status = $transfer->status->value;
        $this->items = is_array($transfer->items) ? $transfer->items : [];
        $this->queue = 'emails';
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Stock Transfer #{$this->transferNumber} - Status Update",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.transfer-status',
        );
    }
}
