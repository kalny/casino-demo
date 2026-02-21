<?php

namespace App\Application\UseCase;

readonly class UserResponse
{
    public function __construct(
        public string $id,
        public string $token
    ) {
    }
}
