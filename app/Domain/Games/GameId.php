<?php

namespace App\Domain\Games;

use App\Domain\Exceptions\InvalidArgumentException;

final readonly class GameId
{
    /**
     * @throws InvalidArgumentException
     */
    public function __construct(private int $value = 0)
    {
        if ($value < 0) {
            throw new InvalidArgumentException("Value must be greater than 0");
        }
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
