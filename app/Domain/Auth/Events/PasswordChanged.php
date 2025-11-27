<?php

namespace App\Domain\Auth\Events;

use App\Domain\User\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PasswordChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public User $user,
    ) {}
}
