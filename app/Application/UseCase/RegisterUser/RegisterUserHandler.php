<?php

namespace App\Application\UseCase\RegisterUser;

use App\Application\UseCase\UserResponse;
use App\Domain\Common\ValueObjects\Email;
use App\Domain\Exceptions\InvalidArgumentException;
use App\Domain\Exceptions\UserAlreadyExistsException;
use App\Domain\Services\PasswordHasher;
use App\Domain\Services\TokenManager;
use App\Domain\User\User;
use App\Domain\User\Repository\UserRepository;
use App\Domain\User\UserId;

final class RegisterUserHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly PasswordHasher $passwordHasher,
        private readonly TokenManager $tokenManager
    ) {
    }

    /**
     * @throws UserAlreadyExistsException
     * @throws InvalidArgumentException
     */
    public function handle(RegisterUserCommand $command): UserResponse
    {
        if ($this->userRepository->existsByEmail($command->email)) {
            throw new UserAlreadyExistsException();
        }

        $user = new User(
            id: new UserId(),
            name: $command->name,
            email: new Email($command->email),
            password: $this->passwordHasher->hash($command->password),
            balance: 0
        );

        $this->userRepository->save($user);

        $token = $this->tokenManager->create($user);

        return new UserResponse($user->getId()->getValue(), $token);
    }
}
