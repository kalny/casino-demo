<?php

namespace App\Application\UseCase\LogoutUser;

use App\Domain\Services\TokenManager;
use App\Domain\User\Repository\UserRepository;
use App\Domain\User\UserId;

class LogoutUserHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly TokenManager $tokenManager
    ) {
    }

    public function handle(string $id): void
    {
        $user = $this->userRepository->getById(new UserId($id));
        $this->tokenManager->delete($user);
    }
}
