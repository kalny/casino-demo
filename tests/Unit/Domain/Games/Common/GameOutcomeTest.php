<?php

namespace Tests\Unit\Domain\Games\Common;

use App\Domain\Common\ValueObjects\BetAmount;
use App\Domain\Common\ValueObjects\BetMultiplier;
use App\Domain\Common\ValueObjects\WinAmount;
use App\Domain\Exceptions\InvalidArgumentException;
use App\Domain\Games\Common\GameOutcome;
use App\Domain\Games\Common\OutcomeStatus;
use App\Domain\Games\Dice\DiceSpecificOutcome;
use App\Domain\Games\Dice\ValueObjects\DiceNumber;
use App\Domain\Games\GameId;
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
            gameId: new GameId(1),
            userId: new UserId('id'),
            betAmount: new BetAmount(100),
            winAmount: new WinAmount(200),
            outcomeStatus: OutcomeStatus::Win,
            gameSpecificOutcome: new DiceSpecificOutcome(
                multiplier: new BetMultiplier(2),
                roll: new DiceNumber(3)
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
            gameId: new GameId(1),
            userId: new UserId('id'),
            betAmount: new BetAmount(100),
            winAmount: WinAmount::zero(),
            outcomeStatus: OutcomeStatus::Loss,
            gameSpecificOutcome: new DiceSpecificOutcome(
                multiplier: new BetMultiplier(0),
                roll: new DiceNumber(3)
            )
        );

        $this->assertFalse($testOutcome->isWin());
    }
}
