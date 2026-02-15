<?php

namespace Tests\Unit\Domain\Games\Dice;

use App\Domain\Common\ValueObjects\BetMultiplier;
use App\Domain\Exceptions\InvalidArgumentException;
use App\Domain\Games\Dice\DiceSpecificOutcome;
use App\Domain\Games\Dice\ValueObjects\DiceNumber;
use Tests\TestCase;

class DiceSpecificOutcomeTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     */
    public function testCreateDiceSpecificOutcome(): void
    {
        $diceSpecificOutcome = new DiceSpecificOutcome(
            multiplier: new BetMultiplier(2),
            roll: new DiceNumber(3)
        );

        $this->assertEquals([
            'multiplier' => 2,
            'roll' => 3,
        ], $diceSpecificOutcome->jsonSerialize());
    }
}
