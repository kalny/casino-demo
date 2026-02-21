<?php

namespace Tests\Unit\Domain\Game\Slot;

use App\Domain\Common\ValueObjects\BetAmount;
use App\Domain\Common\Exceptions\InvalidArgumentException;
use App\Domain\Game\GameId;
use App\Domain\Game\Slot\RandomGridGenerator;
use App\Domain\Game\Slot\SlotGame;
use App\Domain\Game\Slot\ValueObjects\Grid;
use App\Domain\Game\Slot\ValueObjects\GridInt;
use App\Domain\Game\Slot\ValueObjects\Paylines;
use App\Domain\Game\Slot\ValueObjects\PlaySlotInput;
use App\Domain\Game\Slot\ValueObjects\ReelStrip;
use App\Domain\Game\Slot\ValueObjects\SymbolsCollection;
use App\Domain\User\UserId;
use Tests\TestCase;

class SlotGameTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     */
    public function testPlaySlotWin(): void
    {
        $resultGrid = new Grid([
            [
                ['name' => 'A', 'multiplier' => 5],
                ['name' => 'A', 'multiplier' => 5],
                ['name' => 'C', 'multiplier' => 7],
            ],
            [
                ['name' => 'D', 'multiplier' => 8],
                ['name' => 'A', 'multiplier' => 5],
                ['name' => 'E', 'multiplier' => 9],
            ],
            [
                ['name' => 'C', 'multiplier' => 7],
                ['name' => 'A', 'multiplier' => 5],
                ['name' => 'A', 'multiplier' => 5],
            ]
        ]);

        $symbolsCollection = SymbolsCollection::fromArray([
            ['name' => 'A', 'multiplier' => 5],
            ['name' => 'B', 'multiplier' => 6],
            ['name' => 'C', 'multiplier' => 7],
            ['name' => 'D', 'multiplier' => 8],
            ['name' => 'E', 'multiplier' => 9],
        ]);

        $paylines = Paylines::fromArray([
            [[0, 1], [1, 1], [2, 1]],
            [[0, 0], [1, 1], [2, 2]],
        ]);

        $reelStrip = new ReelStrip(
            ['A', 'A', 'C', 'B', 'D', 'A', 'E', 'E', 'C', 'A', 'A'],
            $symbolsCollection
        );

        $slotGame = new SlotGame(
            gameId: GameId::fromString(1),
            name: 'Slot Game',
            reelsNumber: GridInt::fromInt(3),
            symbolsNumber: GridInt::fromInt(3),
            reelStrip: $reelStrip,
            paylines: $paylines
        );

        $playSlotInput = new PlaySlotInput(
            userId: UserId::fromString('id'),
            betAmount: BetAmount::fromInt(100)
        );

        $randomDiceNumberGenerator = $this->createMock(RandomGridGenerator::class);
        $randomDiceNumberGenerator
            ->expects($this->once())
            ->method('nextGrid')
            ->with(
                GridInt::fromInt(3),
                GridInt::fromInt(3),
                $reelStrip
            )
            ->willReturn($resultGrid);

        $gameOutcome = $slotGame->playSlot($playSlotInput, $randomDiceNumberGenerator);

        $this->assertTrue($gameOutcome->isWin());
        $this->assertSame(1000, $gameOutcome->winAmount->getValue()); // (100 * 5) + (100 * 5)
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testPlaySlotLoss(): void
    {
        $resultGrid = new Grid([
            [
                ['name' => 'A', 'multiplier' => 5],
                ['name' => 'A', 'multiplier' => 5],
                ['name' => 'C', 'multiplier' => 7],
            ],
            [
                ['name' => 'D', 'multiplier' => 8],
                ['name' => 'A', 'multiplier' => 5],
                ['name' => 'E', 'multiplier' => 9],
            ],
            [
                ['name' => 'C', 'multiplier' => 7],
                ['name' => 'A', 'multiplier' => 5],
                ['name' => 'A', 'multiplier' => 5],
            ]
        ]);

        $symbolsCollection = SymbolsCollection::fromArray([
            ['name' => 'A', 'multiplier' => 5],
            ['name' => 'B', 'multiplier' => 6],
            ['name' => 'C', 'multiplier' => 7],
            ['name' => 'D', 'multiplier' => 8],
            ['name' => 'E', 'multiplier' => 9],
        ]);

        $paylines = Paylines::fromArray([
            [[0, 2], [1, 1], [2, 0]],
        ]);

        $reelStrip = new ReelStrip(
            ['A', 'A', 'C', 'B', 'D', 'A', 'E', 'E', 'C', 'A', 'A'],
            $symbolsCollection
        );

        $slotGame = new SlotGame(
            gameId: GameId::fromString(1),
            name: 'Slot Game',
            reelsNumber: GridInt::fromInt(3),
            symbolsNumber: GridInt::fromInt(3),
            reelStrip: $reelStrip,
            paylines: $paylines
        );

        $playSlotInput = new PlaySlotInput(
            userId: UserId::fromString('id'),
            betAmount: BetAmount::fromInt(100)
        );

        $randomDiceNumberGenerator = $this->createMock(RandomGridGenerator::class);
        $randomDiceNumberGenerator
            ->expects($this->once())
            ->method('nextGrid')
            ->with(
                GridInt::fromInt(3),
                GridInt::fromInt(3),
                $reelStrip
            )
            ->willReturn($resultGrid);

        $gameOutcome = $slotGame->playSlot($playSlotInput, $randomDiceNumberGenerator);

        $this->assertFalse($gameOutcome->isWin());
        $this->assertSame(0, $gameOutcome->winAmount->getValue());
    }
}
