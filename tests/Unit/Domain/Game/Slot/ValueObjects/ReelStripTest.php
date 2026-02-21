<?php

namespace Tests\Unit\Domain\Game\Slot\ValueObjects;

use App\Domain\Common\Exceptions\InvalidArgumentException;
use App\Domain\Game\Slot\ValueObjects\ReelStrip;
use App\Domain\Game\Slot\ValueObjects\Symbol;
use App\Domain\Game\Slot\ValueObjects\SymbolsCollection;
use Tests\TestCase;

class ReelStripTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     */
    public function testCreateValidReelStripFromArray(): void
    {
        $reelStripArray = ['A', 'B', 'C'];

        $symbolsCollection = new SymbolsCollection([
            ['name' => 'A', 'multiplier' => 5],
            ['name' => 'B', 'multiplier' => 6],
            ['name' => 'C', 'multiplier' => 7],
        ]);

        $reelStrip = new ReelStrip($reelStripArray, $symbolsCollection);

        $this->assertSame(3, count($reelStrip->getData()));
        $this->assertInstanceOf(Symbol::class, $reelStrip->getData()[0]);
        $this->assertInstanceOf(Symbol::class, $reelStrip->getData()[1]);
        $this->assertInstanceOf(Symbol::class, $reelStrip->getData()[2]);
    }

    public function testCreateReelStripFromSmallArray(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $reelStripArray = ['A', 'B'];

        $symbolsCollection = new SymbolsCollection([
            ['name' => 'A', 'multiplier' => 5],
            ['name' => 'B', 'multiplier' => 6],
            ['name' => 'C', 'multiplier' => 7],
        ]);

         new ReelStrip($reelStripArray, $symbolsCollection);
    }
}
