<?php

namespace App\Services\Auth\DTO;

readonly class UserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $token
    ) {
    }
}
