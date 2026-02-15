<?php

namespace App\Domain\Games\Slot\ValueObjects;

use App\Domain\Common\ValueObjects\BetAmount;
use App\Domain\User\UserId;

final readonly class PlaySlotInput
{
    public function __construct(
        public UserId $userId,
        public BetAmount $betAmount,
    ) {
    }
}
