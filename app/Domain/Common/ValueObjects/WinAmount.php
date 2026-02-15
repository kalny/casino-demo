<?php

namespace App\Domain\Common\ValueObjects;

use App\Domain\Exceptions\InvalidArgumentException;

final readonly class WinAmount
{
    /**
     * @throws InvalidArgumentException
     */
    public function __construct(private int $value)
    {
        if ($value < 0) {
            throw new InvalidArgumentException('Value must be positive');
        }
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
