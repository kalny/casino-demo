<?php

namespace App\Application\UseCase\LogoutUser;

use App\Domain\Services\TokenManager;
use App\Domain\User\Repository\UserRepository;

class LogoutUserHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly TokenManager $tokenManager
    ) {
    }

    public function handle(int $id): void
    {
        $user = $this->userRepository->getById($id);
        $this->tokenManager->delete($user);
    }
}
