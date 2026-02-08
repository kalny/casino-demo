<?php

namespace App\Services\Game;

use App\DTO\Api\Game\GameResultDTO;
use App\DTO\Api\Game\PlayGameDTO;

class GameEngineService
{
    public function play(int $gameId, int $userId, PlayGameDTO $playGameDTO): GameResultDTO
    {
        return new GameResultDTO(
            gameId: $gameId,
            userId: $userId,
            amount: $playGameDTO->amount
        );
    }
}
