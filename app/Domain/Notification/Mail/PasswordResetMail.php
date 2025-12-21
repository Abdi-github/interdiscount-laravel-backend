<?php

namespace App\Domain\Notification\Mail;

use App\Domain\User\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $userName;
    public string $resetUrl;

    public function __construct(User $user, string $resetToken)
    {
        $this->userName = $user->first_name;
        $this->resetUrl = config('app.frontend_url', config('app.url'))
            . '/reset-password/' . $resetToken;
        $this->queue = 'emails';
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Your Password - Interdiscount',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.password-reset',
        );
    }
}
