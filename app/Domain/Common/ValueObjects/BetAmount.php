<?php

namespace App\Domain\Common\ValueObjects;

use App\Domain\Common\Exceptions\InvalidArgumentException;

final readonly class BetAmount
{
    /**
     * @throws InvalidArgumentException
     */
    public function __construct(private int $value)
    {
        if ($value <= 0) {
            throw new InvalidArgumentException('The value must be greater than zero');
        }
    }

    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function multiply(BetMultiplier $multiplier): WinAmount
    {
        return new WinAmount($this->value * $multiplier->getValue());
    }
}
