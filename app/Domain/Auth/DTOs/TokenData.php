<?php

namespace App\Domain\Auth\DTOs;

use Spatie\LaravelData\Data;

class TokenData extends Data
{
    public function __construct(
        public string $access_token,
        public string $refresh_token,
        public string $token_type = 'Bearer',
    ) {}
}
