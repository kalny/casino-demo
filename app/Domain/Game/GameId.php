<?php

namespace App\Domain\Game;

final readonly class GameId
{
    private function __construct(private string $value)
    {
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
