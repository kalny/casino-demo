<?php

namespace App\Domain\Common\Services;

interface PasswordHasher
{
    public function hash(string $password): string;
    public function check(string $plainPassword, string $hashedPassword): bool;
}
