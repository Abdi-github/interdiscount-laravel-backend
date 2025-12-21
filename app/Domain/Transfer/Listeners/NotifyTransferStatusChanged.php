<?php

namespace App\Domain\Transfer\Listeners;

use App\Domain\Notification\Mail\TransferStatusUpdateMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyTransferStatusChanged implements ShouldQueue
{
    public string $queue = 'emails';

    public function handle(object $event): void
    {
        $transfer = $event->transfer;
        $transfer->loadMissing(['fromStore', 'toStore', 'initiator']);

        $status = $transfer->status->value ?? $transfer->status;

        Log::info('Transfer status changed', [
            'transfer_id' => $transfer->id,
            'status' => $status,
        ]);

        // Send notification to the initiator's store email
        $fromStore = $transfer->fromStore;
        $toStore = $transfer->toStore;

        $recipients = array_filter([
            $fromStore?->email,
            $toStore?->email,
        ]);

        foreach ($recipients as $email) {
            Mail::to($email)->send(new TransferStatusUpdateMail(
                transfer: $transfer,
                status: $status,
            ));
        }
    }
}
