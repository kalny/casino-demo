<?php

namespace App\Infrastructure\Services;

use App\Domain\Services\PasswordHasher;
use Illuminate\Support\Facades\Hash;

class LaravelPasswordHasher implements PasswordHasher
{
    public function hash(string $password): string
    {
        return Hash::make($password);
    }

    public function check(string $plainPassword, string $hashedPassword): bool
    {
        return Hash::check($plainPassword, $hashedPassword);
    }
}
