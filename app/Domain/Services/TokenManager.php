<?php

namespace App\Domain\Services;

use App\Domain\User\User;

interface TokenManager
{
    public function create(User $user): string;
    public function delete(User $user): void;
}
