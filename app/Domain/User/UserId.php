<?php

namespace App\Domain\User;

use App\Domain\Exceptions\InvalidArgumentException;

final readonly class UserId
{
    /**
     * @throws InvalidArgumentException
     */
    public function __construct(private int $value = 0)
    {
        if ($value < 0) {
            throw new InvalidArgumentException('Value must be positive');
        }
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
