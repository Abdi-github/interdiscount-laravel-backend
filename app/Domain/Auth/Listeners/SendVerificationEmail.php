<?php

namespace App\Domain\Auth\Listeners;

use App\Domain\Auth\Events\UserRegistered;
use App\Domain\Notification\Mail\VerifyEmailMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendVerificationEmail implements ShouldQueue
{
    public string $queue = 'emails';

    public function handle(UserRegistered $event): void
    {
        $user = $event->user;

        Mail::to($user->email)->send(new VerifyEmailMail($user));
    }
}
