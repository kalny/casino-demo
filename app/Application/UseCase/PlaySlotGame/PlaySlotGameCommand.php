<?php

namespace App\Application\UseCase\PlaySlotGame;

final readonly class PlaySlotGameCommand
{
    public function __construct(
        public int $gameId,
        public int $userId,
        public int $betAmount
    ) {
    }

    public static function fromValidated(array $validated, int $gameId, int $userId): self
    {
        return new self(
            gameId: $gameId,
            userId: $userId,
            betAmount: $validated['amount'],
        );
    }
}
