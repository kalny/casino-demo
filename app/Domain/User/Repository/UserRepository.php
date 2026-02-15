<?php

namespace App\Domain\User\Repository;

use App\Domain\User\User;

interface UserRepository
{
    public function existsByEmail(string $email): bool;
    public function findByEmail(string $email): ?User;
    public function getById(int $id): User;
    public function save(User $user): void;
}
