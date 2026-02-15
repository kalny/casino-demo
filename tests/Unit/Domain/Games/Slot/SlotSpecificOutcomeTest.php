<?php

namespace Tests\Unit\Domain\Games\Slot;

use App\Domain\Common\ValueObjects\BetMultiplier;
use App\Domain\Exceptions\InvalidArgumentException;
use App\Domain\Games\Slot\SlotSpecificOutcome;
use App\Domain\Games\Slot\ValueObjects\Grid;
use App\Domain\Games\Slot\ValueObjects\WinningPaylines;
use Tests\TestCase;

class SlotSpecificOutcomeTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     */
    public function testCreateSlotSpecificOutcome(): void
    {
        $grid = new Grid([
            [
                ['name' => 'A', 'multiplier' => 5],
                ['name' => 'B', 'multiplier' => 6],
                ['name' => 'C', 'multiplier' => 7],
            ],
            [
                ['name' => 'A', 'multiplier' => 5],
                ['name' => 'B', 'multiplier' => 6],
                ['name' => 'C', 'multiplier' => 7],
            ],
            [
                ['name' => 'A', 'multiplier' => 5],
                ['name' => 'B', 'multiplier' => 6],
                ['name' => 'C', 'multiplier' => 7],
            ]
        ]);

        $slotSpecificOutcome = new SlotSpecificOutcome(
            multiplier: new BetMultiplier(0),
            grid:$grid,
            winningPaylines: new WinningPaylines([], new BetMultiplier(0))
        );

        $this->assertEquals([
            'multiplier' => 0,
            'grid' => [
                ['A', 'B', 'C'],
                ['A', 'B', 'C'],
                ['A', 'B', 'C'],
            ],
            'winning_paylines' => [],
        ], $slotSpecificOutcome->jsonSerialize());
    }
}
