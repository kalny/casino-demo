<?php

namespace App\Infrastructure\Services;

use App\Domain\Exceptions\InvalidArgumentException;
use App\Domain\Games\Services\RandomGridGenerator;
use App\Domain\Games\Slot\ValueObjects\Grid;
use App\Domain\Games\Slot\ValueObjects\GridInt;
use App\Domain\Games\Slot\ValueObjects\ReelStrip;
use Throwable;

class PHPRandomGridGenerator implements RandomGridGenerator
{
    /**
     * @throws InvalidArgumentException
     */
    public function nextGrid(GridInt $reelsNumber, GridInt $symbolsNumber, ReelStrip $reelStrip): Grid
    {
        $reelStripSymbols = $reelStrip->getData();

        $grid = [];

        for ($i = 0; $i < $reelsNumber->getValue(); $i++) {
            $pos = 0;

            try {
                $pos = random_int(0, count($reelStripSymbols) - 1);
            } catch (Throwable) {}

            $reel = [];

            for ($j = 0; $j < $symbolsNumber->getValue(); $j++) {
                $reel[] = $reelStripSymbols[($pos + $j) % count($reelStripSymbols)];
            }

            $grid[] = $reel;
        }

        return new Grid($grid);
    }
}
