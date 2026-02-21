<?php

namespace App\Domain\Game\Slot\ValueObjects;

use App\Domain\Common\Exceptions\InvalidArgumentException;

final readonly class GridInt
{
    /**
     * @throws InvalidArgumentException
     */
    public function __construct(private int $value)
    {
        if ($this->value < 2) {
            throw new InvalidArgumentException('Invalid GridInt value');
        }
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
