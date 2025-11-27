<?php

namespace App\Domain\Auth\DTOs;

use Spatie\LaravelData\Data;

class RegisterData extends Data
{
    public function __construct(
        public string $email,
        public string $password,
        public string $first_name,
        public string $last_name,
        public ?string $phone = null,
        public string $preferred_language = 'de',
    ) {}
}
