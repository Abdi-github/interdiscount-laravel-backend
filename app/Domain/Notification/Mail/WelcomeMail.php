<?php

namespace App\Domain\Notification\Mail;

use App\Domain\User\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $userName;
    public string $shopUrl;

    public function __construct(User $user)
    {
        $this->userName = $user->first_name;
        $this->shopUrl = config('app.frontend_url', config('app.url'));
        $this->queue = 'emails';
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Interdiscount',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome',
        );
    }
}
