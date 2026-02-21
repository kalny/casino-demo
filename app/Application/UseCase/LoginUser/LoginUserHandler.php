<?php

namespace App\Application\UseCase\LoginUser;

use App\Application\UseCase\UserResponse;
use App\Domain\User\Exceptions\InvalidCredentialsException;
use App\Domain\Common\Services\PasswordHasher;
use App\Application\Services\TokenManager;
use App\Domain\User\Repository\UserRepository;

class LoginUserHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly PasswordHasher $passwordHasher,
        private readonly TokenManager $tokenManager
    ) {
    }

    /**
     * @throws InvalidCredentialsException
     */
    public function handle(LoginUserCommand $command): UserResponse
    {
        $user = $this->userRepository->findByEmail($command->email);

        if (!$user || !$user->checkPassword($command->password, $this->passwordHasher)) {
            throw new InvalidCredentialsException();
        }

        $token = $this->tokenManager->create($user);

        return new UserResponse($user->getId()->getValue(), $token);
    }
}
