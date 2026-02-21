<?php

namespace App\Domain\Game\Slot\ValueObjects;

use App\Domain\Common\Exceptions\InvalidArgumentException;

final readonly class SymbolName
{
    private const MIN_LENGTH = 1;

    /**
     * @throws InvalidArgumentException
     */
    private function __construct(private string $value)
    {
        if (strlen($this->value) < self::MIN_LENGTH) {
            throw new InvalidArgumentException("Symbol name must not be empty");
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    public static function fromString(string $value): self
    {
        return new self($value);
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
