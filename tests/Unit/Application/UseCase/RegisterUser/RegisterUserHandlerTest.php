<?php

namespace Tests\Unit\Application\UseCase\RegisterUser;

use App\Application\UseCase\RegisterUser\RegisterUserCommand;
use App\Application\UseCase\RegisterUser\RegisterUserHandler;
use App\Domain\Exceptions\InvalidArgumentException;
use App\Domain\Exceptions\UserAlreadyExistsException;
use App\Domain\Services\IdGenerator;
use App\Domain\Services\PasswordHasher;
use App\Domain\Services\TokenManager;
use App\Domain\User\Repository\UserRepository;
use Tests\TestCase;

class RegisterUserHandlerTest extends TestCase
{
    private UserRepository $userRepository;
    private TokenManager $tokenManager;
    private RegisterUserHandler $registerUserHandler;
    private IdGenerator $idGenerator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->createMock(UserRepository::class);
        $passwordHasher = $this->createMock(PasswordHasher::class);
        $this->tokenManager = $this->createMock(TokenManager::class);
        $this->idGenerator = $this->createMock(IdGenerator::class);

        $this->idGenerator
            ->expects($this->any())
            ->method('generate')
            ->willReturn('id');

        $this->registerUserHandler = new RegisterUserHandler(
            userRepository:  $this->userRepository,
            passwordHasher: $passwordHasher,
            tokenManager: $this->tokenManager,
            idGenerator: $this->idGenerator
        );
    }

    /**
     * @throws UserAlreadyExistsException
     * @throws InvalidArgumentException
     */
    public function testRegisterUserSuccess(): void
    {
        $this->tokenManager
            ->expects($this->once())
            ->method('create')
            ->willReturn('token');

        $this->userRepository
            ->expects($this->once())
            ->method('existsByEmail')
            ->with('test@example.com')
            ->willReturn(false);

        $this->userRepository
            ->expects($this->once())
            ->method('save');

        $command = new RegisterUserCommand('Test', 'test@example.com', 'password');

        $result = $this->registerUserHandler->handle($command);

        $this->assertSame('token', $result->token);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testRegisterUserAlreadyExists(): void
    {
        $this->expectException(UserAlreadyExistsException::class);

        $this->userRepository
            ->expects($this->once())
            ->method('existsByEmail')
            ->with('test@example.com')
            ->willReturn(true);

        $command = new RegisterUserCommand('Test', 'test@example.com', 'password');

        $this->registerUserHandler->handle($command);
    }
}
