<?php

namespace App\Domain\Common\ValueObjects;

use App\Domain\Common\Exceptions\InvalidArgumentException;

final readonly class WinAmount
{
    /**
     * @throws InvalidArgumentException
     */
    private function __construct(private int $value)
    {
        if ($value < 0) {
            throw new InvalidArgumentException('Value must be positive');
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
    public static function zero(): self
    {
        return new self(0);
    }
}
