<?php

namespace App\Domain\Game;

abstract class Game
{
    public function __construct(
        protected GameId $gameId,
        protected GameType $type,
        protected string $name
    ) {
    }

    public function getId(): GameId
    {
        return $this->gameId;
    }
}
