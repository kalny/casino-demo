<?php

namespace App\Domain\User;

use App\Domain\Common\ValueObjects\Email;
use App\Domain\Exceptions\InsufficientFundsException;
use App\Domain\Common\ValueObjects\BetAmount;
use App\Domain\Common\ValueObjects\WinAmount;
use App\Domain\Services\PasswordHasher;

class User
{
    public function __construct(
        private UserId $id,
        private string $name,
        private Email $email,
        private string $password,
        private int $balance
    ) {
    }

    public function checkPassword(string $plainPassword, PasswordHasher $hasher): bool
    {
        return $hasher->check($plainPassword, $this->password);
    }

    public function getId(): UserId
    {
        return $this->id;
    }

    public function equals(self $other): bool
    {
        return $this->id->equals($other->id);
    }

    /**
     * @throws InsufficientFundsException
     */
    public function debit(BetAmount $betAmount): void
    {
        if ($this->balance < $betAmount->getValue()) {
            throw new InsufficientFundsException('Insufficient balance');
        }

        $this->balance -= $betAmount->getValue();
    }

    public function credit(WinAmount $winAmount): void
    {
        $this->balance += $winAmount->getValue();
    }

    public function getBalance(): int
    {
        return $this->balance;
    }
}
