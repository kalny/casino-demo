<?php

namespace App\Domain\Games\Repository;

use App\Domain\Games\Common\GameOutcome;

interface GameOutcomeRepository
{
    public function save(GameOutcome $gameOutcome): void;
}
