<?php

namespace App\Application\UseCase\LoginUser;

final readonly class LoginUserCommand
{
    public function __construct(
        public string $email,
        public string $password
    ) {
    }
}
