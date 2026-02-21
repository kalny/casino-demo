<?php

namespace App\Domain\Game\Repository;

use App\Domain\Game\GameType;
use App\Domain\Game\Dice\DiceGame;
use App\Domain\Game\GameId;
use App\Domain\Game\Slot\SlotGame;

interface GameRepository
{
    public function getTypeById(GameId $id): GameType;
    public function getDiceGameById(GameId $id): DiceGame;
    public function getSlotGameById(GameId $id): SlotGame;
}
