<?php

namespace App\DTO\Api\Game;

use App\Enums\BetResult;

readonly class GameResultDTO
{
    public function __construct(
        public BetResult $result,
        public int $payout,
        public int $balance,
        public array $playResult,
    ) {
    }
}
