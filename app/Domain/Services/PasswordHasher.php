<?php

namespace App\Domain\Services;

interface PasswordHasher
{
    public function hash(string $password): string;
    public function check(string $plainPassword, string $hashedPassword): bool;
}
