<?php

namespace Tests\Unit\Domain\Game\Slot;

use App\Domain\Common\Exceptions\InvalidArgumentException;
use App\Domain\Game\Slot\ValueObjects\GridInt;
use App\Domain\Game\Slot\ValueObjects\ReelStrip;
use App\Domain\Game\Slot\ValueObjects\SymbolsCollection;
use App\Domain\Game\Slot\RandomGridGenerator;
use App\Domain\Common\Services\RandomNumberGenerator;
use Tests\TestCase;

class RandomGridGeneratorTest extends TestCase
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

        $rng = $this->createMock(RandomNumberGenerator::class);
        $rng
            ->expects($this->any())
            ->method('getNextRandom')
            ->willReturn(2);

        $randomGridGenerator = new RandomGridGenerator($rng);
        $grid = $randomGridGenerator->nextGrid(
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
