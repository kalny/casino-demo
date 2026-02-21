<?php

namespace App\Domain\User\Repository;

use App\Domain\User\User;
use App\Domain\User\UserId;

interface UserRepository
{
    public function existsByEmail(string $email): bool;
    public function findByEmail(string $email): ?User;
    public function getById(UserId $id): User;
    public function save(User $user): void;
}
