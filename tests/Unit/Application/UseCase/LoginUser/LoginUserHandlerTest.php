<?php

namespace Tests\Unit\Application\UseCase\LoginUser;

use App\Application\UseCase\LoginUser\LoginUserCommand;
use App\Application\UseCase\LoginUser\LoginUserHandler;
use App\Domain\User\Exceptions\InvalidCredentialsException;
use App\Domain\Common\Services\PasswordHasher;
use App\Application\Services\TokenManager;
use App\Domain\User\Repository\UserRepository;
use App\Domain\User\User;
use App\Domain\User\UserId;
use Tests\TestCase;

class LoginUserHandlerTest extends TestCase
{
    private UserRepository $userRepository;
    private PasswordHasher $passwordHasher;
    private TokenManager $tokenManager;
    private LoginUserHandler $loginUserHandler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->createMock(UserRepository::class);
        $this->passwordHasher = $this->createMock(PasswordHasher::class);
        $this->tokenManager = $this->createMock(TokenManager::class);

        $this->loginUserHandler = new LoginUserHandler(
            userRepository:  $this->userRepository,
            passwordHasher: $this->passwordHasher,
            tokenManager: $this->tokenManager
        );
    }

    /**
     * @throws InvalidCredentialsException
     */
    public function testLoginUserSuccess(): void
    {
        $user = $this->createMock(User::class);

        $user
            ->expects($this->once())
            ->method('checkPassword')
            ->with('password', $this->passwordHasher)
            ->willReturn(true);

        $user
            ->expects($this->once())
            ->method('getId')
            ->willReturn(new UserId('id'));

        $this->tokenManager
            ->expects($this->once())
            ->method('create')
            ->with($user)
            ->willReturn('token');

        $this->userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->willReturn($user);

        $command = new LoginUserCommand('test@example.com', 'password');

        $result = $this->loginUserHandler->handle($command);

        $this->assertSame('token', $result->token);
        $this->assertSame('id', $result->id);
    }

    public function testLoginUserNotFound(): void
    {
        $this->expectException(InvalidCredentialsException::class);

        $this->userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->willReturn(null);

        $command = new LoginUserCommand('test@example.com', 'password');

        $this->loginUserHandler->handle($command);
    }

    public function testLoginUserIncorrectPassword(): void
    {
        $this->expectException(InvalidCredentialsException::class);

        $user = $this->createMock(User::class);

        $user
            ->expects($this->once())
            ->method('checkPassword')
            ->with('password', $this->passwordHasher)
            ->willReturn(false);

        $this->userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->willReturn($user);

        $command = new LoginUserCommand('test@example.com', 'password');

        $this->loginUserHandler->handle($command);
    }
}
