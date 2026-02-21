<?php

namespace Tests\Unit\Domain\Game\Dice;

use App\Domain\Common\ValueObjects\BetMultiplier;
use App\Domain\Common\Exceptions\InvalidArgumentException;
use App\Domain\Game\Dice\DiceSpecificOutcome;
use App\Domain\Game\Dice\ValueObjects\DiceNumber;
use Tests\TestCase;

class DiceSpecificOutcomeTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     */
    public function testCreateDiceSpecificOutcome(): void
    {
        $diceSpecificOutcome = new DiceSpecificOutcome(
            multiplier: BetMultiplier::fromInt(2),
            roll: DiceNumber::fromInt(3)
        );

        $this->assertEquals([
            'multiplier' => 2,
            'roll' => 3,
        ], $diceSpecificOutcome->jsonSerialize());
    }
}
