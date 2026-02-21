<?php

namespace App\Domain\Game\Repository;

use App\Domain\Game\GameOutcome;

interface GameOutcomeRepository
{
    public function save(GameOutcome $gameOutcome): void;
}
