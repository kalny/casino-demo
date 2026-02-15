<?php

namespace App\Domain\Games\Slot\ValueObjects;

use App\Domain\Exceptions\InvalidArgumentException;

final readonly class SymbolName
{
    private const MIN_LENGTH = 1;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(private string $value)
    {
        if (strlen($this->value) < self::MIN_LENGTH) {
            throw new InvalidArgumentException("Symbol name must not be empty");
        }
    }

    public function equals(string $symbolName): bool
    {
        return $this->value === $symbolName;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
