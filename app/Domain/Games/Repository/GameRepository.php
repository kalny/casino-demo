<?php

namespace App\Domain\Games\Repository;

use App\Domain\Games\Common\GameType;
use App\Domain\Games\Dice\DiceGame;
use App\Domain\Games\Slot\SlotGame;

interface GameRepository
{
    public function getTypeById(int $id): GameType;
    public function getDiceGameById(int $id): DiceGame;
    public function getSlotGameById(int $id): SlotGame;
}
