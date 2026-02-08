<?php

namespace App\DTO\Api\Game;

readonly class GameResultDTO
{
    public function __construct(
        public int $gameId,
        public int $amount
    ) {
    }
}
