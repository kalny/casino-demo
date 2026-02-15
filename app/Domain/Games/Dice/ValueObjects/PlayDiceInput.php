<?php

namespace App\Domain\Games\Dice\ValueObjects;

use App\Domain\Common\ValueObjects\BetAmount;
use App\Domain\User\UserId;

final readonly class PlayDiceInput
{
    public function __construct(
        public UserId $userId,
        public BetAmount $betAmount,
        public DiceNumber $chosenNumber,
        public PlayDiceType $playDiceType,
    ) {
    }

    public function isOver(): bool
    {
        return $this->playDiceType === PlayDiceType::Over;
    }
}
