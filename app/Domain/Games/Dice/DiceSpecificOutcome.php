<?php

namespace App\Domain\Games\Dice;

use App\Domain\Common\ValueObjects\BetMultiplier;
use App\Domain\Games\Common\GameSpecificOutcome;
use App\Domain\Games\Dice\ValueObjects\DiceNumber;

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
