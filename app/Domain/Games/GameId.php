<?php

namespace App\Domain\Games;

final readonly class GameId
{
    public function __construct(private string $value)
    {
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
