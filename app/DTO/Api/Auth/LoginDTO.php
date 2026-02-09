<?php

namespace App\DTO\Api\Auth;

readonly class LoginDTO
{
    public function __construct(
        public string $email,
        public string $password
    ) {
    }
}
