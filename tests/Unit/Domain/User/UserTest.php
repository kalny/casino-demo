<?php

namespace Tests\Unit\Domain\User;

use App\Domain\Common\ValueObjects\BetAmount;
use App\Domain\Common\ValueObjects\Email;
use App\Domain\Common\ValueObjects\WinAmount;
use App\Domain\Common\Exceptions\InsufficientFundsException;
use App\Domain\Common\Exceptions\InvalidArgumentException;
use App\Domain\Common\Services\PasswordHasher;
use App\Domain\User\User;
use App\Domain\User\UserId;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testCreateValidUser(): void
    {
        $user = new User(
            id: UserId::fromString('id'),
            name: 'Test User',
            email: Email::fromString('test@example.com'),
            password: 'password',
            balance: 100
        );

        $this->assertSame('id', $user->getId()->getValue());
    }

    public function testCheckUserPassword(): void
    {
        $hasher = $this->createMock(PasswordHasher::class);

        $hasher
            ->expects($this->once())
            ->method('hash')
            ->with('password')
            ->willReturn('password_hashed');

        $user = new User(
            id: UserId::fromString('id'),
            name: 'Test User',
            email: Email::fromString('test@example.com'),
            password: $hasher->hash('password'),
            balance: 100
        );

        $hasher
            ->expects($this->once())
            ->method('check')
            ->with('password', 'password_hashed')
            ->willReturn(true);

        $checkResult = $user->checkPassword('password', $hasher);

        $this->assertTrue($checkResult);
    }

    public function testEqualsComparesUsersCorrectly(): void
    {
        $user = new User(
            id: UserId::fromString('id'),
            name: 'Test User',
            email: Email::fromString('test@example.com'),
            password: 'password',
            balance: 100
        );

        $anotherUserInstance = new User(
            id: UserId::fromString('id'),
            name: 'Test User',
            email: Email::fromString('test@example.com'),
            password: 'password',
            balance: 100
        );

        $this->assertTrue($user->equals($anotherUserInstance));
    }

    public function testEqualsComparesUserWithAnotherIdCorrectly(): void
    {
        $user = new User(
            id: UserId::fromString('id'),
            name: 'Test User',
            email: Email::fromString('test@example.com'),
            password: 'password',
            balance: 100
        );

        $anotherUserInstance = new User(
            id: UserId::fromString('id2'),
            name: 'Another Test User',
            email: Email::fromString('another@example.com'),
            password: 'password',
            balance: 100
        );

        $this->assertFalse($user->equals($anotherUserInstance));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testCreditBalance(): void
    {
        $user = new User(
            id: UserId::fromString('id'),
            name: 'Test User',
            email: Email::fromString('test@example.com'),
            password: 'password',
            balance: 0
        );

        $winAmount = WinAmount::fromInt(100);
        $user->credit($winAmount);

        $this->assertSame(100, $user->getBalance());
    }

    /**
     * @throws InsufficientFundsException
     * @throws InvalidArgumentException
     */
    public function testSuccessfullyDebitBalance(): void
    {
        $user = new User(
            id: UserId::fromString('id'),
            name: 'Test User',
            email: Email::fromString('test@example.com'),
            password: 'password',
            balance: 100
        );

        $betAmount = BetAmount::fromInt(100);
        $user->debit($betAmount);

        $this->assertSame(0, $user->getBalance());
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testDebitBalanceWithUnsufficientFunds(): void
    {
        $this->expectException(InsufficientFundsException::class);

        $user = new User(
            id: UserId::fromString('id'),
            name: 'Test User',
            email: Email::fromString('test@example.com'),
            password: 'password',
            balance: 100
        );

        $betAmount = BetAmount::fromInt(101);
        $user->debit($betAmount);
    }
}
