<?php

namespace App\Application\UseCase\PlaySlotGame;

final readonly class PlaySlotGameCommand
{
    public function __construct(
        public string $gameId,
        public string $userId,
        public int $betAmount
    ) {
    }

    public static function fromValidated(array $validated, string $gameId, string $userId): self
    {
        return new self(
            gameId: $gameId,
            userId: $userId,
            betAmount: $validated['amount'],
        );
    }
}
