<?php

namespace App\Domain\Auth\Listeners;

use App\Domain\Auth\Events\PasswordChanged;
use App\Domain\Notification\Mail\PasswordChangedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendPasswordChangedEmail implements ShouldQueue
{
    public string $queue = 'emails';

    public function handle(PasswordChanged $event): void
    {
        $user = $event->user;

        Mail::to($user->email)->send(new PasswordChangedMail($user));
    }
}
