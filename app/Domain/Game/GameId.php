<?php

namespace App\Domain\Game;

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
