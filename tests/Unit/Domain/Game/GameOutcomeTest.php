<?php

namespace Tests\Unit\Domain\Game;

use App\Domain\Common\ValueObjects\BetAmount;
use App\Domain\Common\ValueObjects\BetMultiplier;
use App\Domain\Common\ValueObjects\WinAmount;
use App\Domain\Common\Exceptions\InvalidArgumentException;
use App\Domain\Game\GameOutcome;
use App\Domain\Game\OutcomeStatus;
use App\Domain\Game\Dice\DiceSpecificOutcome;
use App\Domain\Game\Dice\ValueObjects\DiceNumber;
use App\Domain\Game\GameId;
use App\Domain\User\UserId;
use Tests\TestCase;

class GameOutcomeTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     */
    public function testCreateTestOutcomeWithWinStatus(): void
    {
        $testOutcome = new GameOutcome(
            gameId: GameId::fromString(1),
            userId: UserId::fromString('id'),
            betAmount: BetAmount::fromInt(100),
            winAmount: WinAmount::fromInt(200),
            outcomeStatus: OutcomeStatus::Win,
            gameSpecificOutcome: new DiceSpecificOutcome(
                multiplier: BetMultiplier::fromInt(2),
                roll: DiceNumber::fromInt(3)
            )
        );

        $this->assertTrue($testOutcome->isWin());
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testCreateTestOutcomeWithLossStatus(): void
    {
        $testOutcome = new GameOutcome(
            gameId: GameId::fromString(1),
            userId: UserId::fromString('id'),
            betAmount: BetAmount::fromInt(100),
            winAmount: WinAmount::zero(),
            outcomeStatus: OutcomeStatus::Loss,
            gameSpecificOutcome: new DiceSpecificOutcome(
                multiplier: BetMultiplier::fromInt(0),
                roll: DiceNumber::fromInt(3)
            )
        );

        $this->assertFalse($testOutcome->isWin());
    }
}
