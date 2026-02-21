<?php

namespace App\Domain\Game\Dice;

use App\Domain\Common\ValueObjects\BetMultiplier;
use App\Domain\Game\GameSpecificOutcome;
use App\Domain\Game\Dice\ValueObjects\DiceNumber;

final readonly class DiceSpecificOutcome extends GameSpecificOutcome
{
    public function __construct(public BetMultiplier $multiplier, public DiceNumber $roll)
    {
    }

    public function jsonSerialize(): mixed
    {
        return [
            'multiplier' => $this->multiplier->getValue(),
            'roll' => $this->roll->getValue(),
        ];
    }
}
