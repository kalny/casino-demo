<?php

namespace App\Services\Game;

use App\Enums\GameType;
use App\Services\Game\Contracts\GameFactory;
use App\Services\Game\Contracts\GameService;
use App\Services\Game\Contracts\RNGService;
use App\Services\Game\Dice\DiceGameService;
use App\Services\Game\Dice\DiceRNGService;
use App\Services\Game\Slot\SlotGameService;
use App\Services\Game\Slot\SlotRNGService;

class GameResolver implements GameFactory
{
    public function getGameService(GameType $gameType): GameService
    {
        return match ($gameType) {
            GameType::Dice => new DiceGameService(),
            GameType::Slot => new SlotGameService(),
        };
    }

    public function getRNGService(GameType $gameType): RNGService
    {
        return match ($gameType) {
            GameType::Dice => new DiceRNGService(),
            GameType::Slot => new SlotRNGService(),
        };
    }
}
