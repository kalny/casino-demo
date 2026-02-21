<?php

namespace App\Domain\Common\ValueObjects;

use App\Domain\Common\Exceptions\InvalidArgumentException;

final readonly class BetMultiplier
{
    /**
     * @throws InvalidArgumentException
     */
    private function __construct(private int $value)
    {
        if ($value < 0) {
            throw new InvalidArgumentException('the multiplier cannot be negative');
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

    public function gt(self $other): bool
    {
        return $this->value > $other->value;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function add(self $other): self
    {
        return new self($this->value + $other->value);
    }
}
