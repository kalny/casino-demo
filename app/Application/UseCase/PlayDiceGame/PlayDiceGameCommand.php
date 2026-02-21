<?php

namespace App\Application\UseCase\PlayDiceGame;

use App\Domain\Common\Exceptions\InvalidArgumentException;

final readonly class PlayDiceGameCommand
{
    private function __construct(
        public string $gameId,
        public string $userId,
        public int $betAmount,
        public int $chosenNumber,
        public string $playDiceType,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public static function fromValidated(array $validated, string $gameId, string $userId): self
    {
        if (!isset($validated['params']['number']) || !isset($validated['params']['bet_type'])) {
            throw new InvalidArgumentException('params.number and params.bet_type are required.');
        }
        return new self(
            gameId: $gameId,
            userId: $userId,
            betAmount: $validated['amount'],
            chosenNumber: $validated['params']['number'],
            playDiceType: $validated['params']['bet_type'],
        );
    }
}
