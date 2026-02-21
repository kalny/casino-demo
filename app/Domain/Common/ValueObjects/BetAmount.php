<?php

namespace App\Domain\Common\ValueObjects;

use App\Domain\Common\Exceptions\InvalidArgumentException;

final readonly class BetAmount
{
    /**
     * @throws InvalidArgumentException
     */
    private function __construct(private int $value)
    {
        if ($value <= 0) {
            throw new InvalidArgumentException('The value must be greater than zero');
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    public static function fromInt(int $value): self
    {
        return new self($value);
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
        return WinAmount::fromInt($this->value * $multiplier->getValue());
    }
}
