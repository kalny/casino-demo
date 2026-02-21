<?php

namespace App\Domain\Games\Repository;

use App\Domain\Games\Common\GameType;
use App\Domain\Games\Dice\DiceGame;
use App\Domain\Games\GameId;
use App\Domain\Games\Slot\SlotGame;

interface GameRepository
{
    public function getTypeById(GameId $id): GameType;
    public function getDiceGameById(GameId $id): DiceGame;
    public function getSlotGameById(GameId $id): SlotGame;
}
