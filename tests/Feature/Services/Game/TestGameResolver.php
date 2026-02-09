<?php

namespace Tests\Feature\Services\Game;

use App\Enums\GameType;
use App\Services\Game\Dice\DiceGameService;
use App\Services\Game\Contracts\GameFactory;
use App\Services\Game\Contracts\GameService;
use App\Services\Game\Contracts\RNGService;
use App\Services\Game\Slot\SlotGameService;

readonly class TestGameResolver implements GameFactory
{
    public function __construct(private int $roll, private array $reels)
    {
    }

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
            GameType::Dice => new DumbDiceRNGService($this->roll),
            GameType::Slot => new DumbSlotRNGService($this->reels),
        };
    }
}
