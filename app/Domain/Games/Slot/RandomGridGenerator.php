<?php

namespace App\Domain\Games\Slot;

use App\Domain\Exceptions\InvalidArgumentException;
use App\Domain\Games\Slot\ValueObjects\Grid;
use App\Domain\Games\Slot\ValueObjects\GridInt;
use App\Domain\Games\Slot\ValueObjects\ReelStrip;
use App\Domain\Services\RandomNumberGenerator;

class RandomGridGenerator
{
    public function __construct(private readonly RandomNumberGenerator $generator)
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function nextGrid(GridInt $reelsNumber, GridInt $symbolsNumber, ReelStrip $reelStrip): Grid
    {
        $reelStripSymbols = $reelStrip->getData();

        $grid = [];

        for ($i = 0; $i < $reelsNumber->getValue(); $i++) {
            $pos = $this->generator->getNextRandom(0, count($reelStripSymbols) - 1);

            $reel = [];

            for ($j = 0; $j < $symbolsNumber->getValue(); $j++) {
                $reel[] = $reelStripSymbols[($pos + $j) % count($reelStripSymbols)];
            }

            $grid[] = $reel;
        }

        return new Grid($grid);
    }
}
