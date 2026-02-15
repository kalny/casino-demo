<?php

namespace Tests\Unit\Infrastructure\Services;

use App\Domain\Exceptions\InvalidArgumentException;
use App\Domain\Games\Slot\ValueObjects\GridInt;
use App\Domain\Games\Slot\ValueObjects\ReelStrip;
use App\Domain\Games\Slot\ValueObjects\SymbolsCollection;
use App\Infrastructure\Services\PHPRandomGridGenerator;
use Tests\TestCase;

class PHPRandomGridGeneratorTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     */
    public function testNextNumber(): void
    {
        $symbolsCollection = new SymbolsCollection([
            ['name' => 'A', 'multiplier' => 5],
            ['name' => 'B', 'multiplier' => 6],
            ['name' => 'C', 'multiplier' => 7],
            ['name' => 'D', 'multiplier' => 8],
            ['name' => 'E', 'multiplier' => 9],
        ]);
        $reelStrip = new ReelStrip(
            ['A', 'A', 'C', 'B', 'D', 'A', 'E', 'E', 'C', 'A', 'A'],
            $symbolsCollection
        );

        $rng = new PHPRandomGridGenerator();
        $grid = $rng->nextGrid(
            new GridInt(3),
            new GridInt(3),
            $reelStrip
        );

        $this->assertSame(3, count($grid->getData()));

        foreach ($grid->getData() as $reel) {
            $this->assertSame(3, count($reel->getData()));
        }
    }
}
