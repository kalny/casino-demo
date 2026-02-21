<?php

namespace Tests\Unit\Domain\Game\Slot;

use App\Domain\Common\ValueObjects\BetMultiplier;
use App\Domain\Common\Exceptions\InvalidArgumentException;
use App\Domain\Game\Slot\SlotSpecificOutcome;
use App\Domain\Game\Slot\ValueObjects\Grid;
use App\Domain\Game\Slot\ValueObjects\WinningPaylines;
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
