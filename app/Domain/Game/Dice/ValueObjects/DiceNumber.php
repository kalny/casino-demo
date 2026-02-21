<?php

namespace App\Domain\Game\Dice\ValueObjects;

use App\Domain\Common\Exceptions\InvalidArgumentException;

final readonly class DiceNumber
{
    public const MIN = 1;
    public const MAX = 6;

    /**
     * @throws InvalidArgumentException
     */
    private function __construct(private int $value)
    {
        if ($this->value < self::MIN || $this->value > self::MAX) {
            throw new InvalidArgumentException('Chosen Number must be between 1 and 6');
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    public static function fromInt(int $value): self
    {
        return new self($value);
    }

    public function gt(self $other): bool
    {
        return $this->value > $other->value;
    }

    public function lt(self $other): bool
    {
        return $this->value < $other->value;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
