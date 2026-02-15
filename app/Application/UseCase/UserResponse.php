<?php

namespace App\Application\UseCase;

readonly class UserResponse
{
    public function __construct(
        public int $id,
        public string $token
    ) {
    }
}
