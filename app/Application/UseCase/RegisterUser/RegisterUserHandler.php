<?php

namespace App\Application\UseCase\RegisterUser;

use App\Application\UseCase\UserResponse;
use App\Domain\Common\ValueObjects\Email;
use App\Domain\Common\Exceptions\InvalidArgumentException;
use App\Domain\User\Exceptions\UserAlreadyExistsException;
use App\Application\Services\IdGenerator;
use App\Domain\Common\Services\PasswordHasher;
use App\Application\Services\TokenManager;
use App\Domain\User\User;
use App\Domain\User\Repository\UserRepository;
use App\Domain\User\UserId;

final class RegisterUserHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly PasswordHasher $passwordHasher,
        private readonly TokenManager $tokenManager,
        private readonly IdGenerator $idGenerator
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
            id: UserId::fromString($this->idGenerator->generate()),
            name: $command->name,
            email: Email::fromString($command->email),
            password: $this->passwordHasher->hash($command->password),
            balance: 0
        );

        $this->userRepository->save($user);

        $token = $this->tokenManager->create($user);

        return new UserResponse($user->getId()->getValue(), $token);
    }
}
