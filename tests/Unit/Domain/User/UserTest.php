<?php

namespace Tests\Unit\Domain\User;

use App\Domain\Common\ValueObjects\BetAmount;
use App\Domain\Common\ValueObjects\Email;
use App\Domain\Common\ValueObjects\WinAmount;
use App\Domain\Exceptions\InsufficientFundsException;
use App\Domain\Exceptions\InvalidArgumentException;
use App\Domain\Services\PasswordHasher;
use App\Domain\User\User;
use App\Domain\User\UserId;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     */
    public function testCreateValidUser(): void
    {
        $user = new User(
            id: new UserId(1),
            name: 'Test User',
            email: new Email('test@example.com'),
            password: 'password',
            balance: 100
        );

        $this->assertSame(1, $user->getId()->getValue());
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testCheckUserPassword(): void
    {
        $hasher = $this->createMock(PasswordHasher::class);

        $hasher
            ->expects($this->once())
            ->method('hash')
            ->with('password')
            ->willReturn('password_hashed');

        $user = new User(
            id: new UserId(1),
            name: 'Test User',
            email: new Email('test@example.com'),
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

    /**
     * @throws InvalidArgumentException
     */
    public function testEqualsComparesUsersCorrectly(): void
    {
        $user = new User(
            id: new UserId(1),
            name: 'Test User',
            email: new Email('test@example.com'),
            password: 'password',
            balance: 100
        );

        $anotherUserInstance = new User(
            id: new UserId(1),
            name: 'Test User',
            email: new Email('test@example.com'),
            password: 'password',
            balance: 100
        );

        $this->assertTrue($user->equals($anotherUserInstance));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testEqualsComparesUserWithAnotherIdCorrectly(): void
    {
        $user = new User(
            id: new UserId(1),
            name: 'Test User',
            email: new Email('test@example.com'),
            password: 'password',
            balance: 100
        );

        $anotherUserInstance = new User(
            id: new UserId(2),
            name: 'Another Test User',
            email: new Email('another@example.com'),
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
            id: new UserId(1),
            name: 'Test User',
            email: new Email('test@example.com'),
            password: 'password',
            balance: 0
        );

        $winAmount = new WinAmount(100);
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
            id: new UserId(1),
            name: 'Test User',
            email: new Email('test@example.com'),
            password: 'password',
            balance: 100
        );

        $betAmount = new BetAmount(100);
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
            id: new UserId(1),
            name: 'Test User',
            email: new Email('test@example.com'),
            password: 'password',
            balance: 100
        );

        $betAmount = new BetAmount(101);
        $user->debit($betAmount);
    }
}
