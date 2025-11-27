<?php

namespace App\Domain\Auth\Listeners;

use App\Domain\Auth\Events\UserEmailVerified;
use App\Domain\Notification\Mail\WelcomeMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail implements ShouldQueue
{
    public string $queue = 'emails';

    public function handle(UserEmailVerified $event): void
    {
        $user = $event->user;

        Mail::to($user->email)->send(new WelcomeMail($user));
    }
}
