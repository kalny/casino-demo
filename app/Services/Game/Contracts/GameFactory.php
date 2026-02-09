<?php

namespace App\Services\Game\Contracts;

use App\Enums\GameType;

interface GameFactory
{
    public function getGameService(GameType $gameType): GameService;
    public function getRNGService(GameType $gameType): RNGService;
}
