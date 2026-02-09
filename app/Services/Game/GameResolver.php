<?php

namespace App\Services\Game;

use App\Enums\GameType;

interface GameResolver
{
    public function getGameService(GameType $gameType): GameService;
    public function getRNGService(GameType $gameType): RNGService;
}
