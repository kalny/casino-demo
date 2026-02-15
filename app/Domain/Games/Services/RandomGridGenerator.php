<?php

namespace App\Domain\Games\Services;

use App\Domain\Games\Slot\ValueObjects\Grid;
use App\Domain\Games\Slot\ValueObjects\GridInt;
use App\Domain\Games\Slot\ValueObjects\ReelStrip;

interface RandomGridGenerator
{
    public function nextGrid(GridInt $reelsNumber, GridInt $symbolsNumber, ReelStrip $reelStrip): Grid;
}
