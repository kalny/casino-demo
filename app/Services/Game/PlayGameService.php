<?php

namespace App\Services\Game;

use App\DTO\Api\Game\PlayGameDTO;
use App\Models\Game;
use App\Services\Game\Contracts\GameFactory;

class PlayGameService
{
    public function play(GameFactory $gameResolver, Game $game, PlayGameDTO $playGameDTO): array
    {
        $rngService = $gameResolver->getRngService($game->type);
        $gameService = $gameResolver->getGameService($game->type);

        return $gameService->play($game, $rngService, $playGameDTO->params);
    }
}
