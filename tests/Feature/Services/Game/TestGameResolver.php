<?php

namespace Tests\Feature\Services\Game;

use App\Enums\GameType;
use App\Services\Game\DiceGameService;
use App\Services\Game\GameResolver;
use App\Services\Game\GameService;
use App\Services\Game\RNGService;
use App\Services\Game\SlotGameService;

readonly class TestGameResolver implements GameResolver
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
