<?php

namespace App\Domain\Auth\Listeners;

use App\Domain\Auth\Events\PasswordResetRequested;
use App\Domain\Notification\Mail\PasswordResetMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendPasswordResetEmail implements ShouldQueue
{
    public string $queue = 'emails';

    public function handle(PasswordResetRequested $event): void
    {
        $user = $event->user;

        Mail::to($user->email)->send(new PasswordResetMail($user, $event->resetToken));
    }
}
