<?php

namespace App\Services\Game;

use App\Enums\GameType;

class CasinoGameResolver implements GameResolver
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
