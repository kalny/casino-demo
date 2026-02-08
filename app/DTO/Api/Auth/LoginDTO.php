<?php

namespace App\DTO\Api\Auth;

class LoginDTO
{
    public function __construct(
        public string $email,
        public string $password
    ) {
    }
}
