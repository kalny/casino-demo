<?php

namespace App\Domain\Game;

use App\Domain\Common\ValueObjects\BetAmount;
use App\Domain\Common\ValueObjects\WinAmount;
use App\Domain\User\UserId;

final readonly class GameOutcome
{
    public function __construct(
        public GameId $gameId,
        public UserId $userId,
        public BetAmount $betAmount,
        public WinAmount $winAmount,
        public OutcomeStatus $outcomeStatus,
        public GameSpecificOutcome $gameSpecificOutcome
    ) {
    }

    public function isWin(): bool
    {
        return $this->outcomeStatus === OutcomeStatus::Win;
    }
}
