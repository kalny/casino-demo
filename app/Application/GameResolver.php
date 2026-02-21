<?php

namespace App\Application;

use App\Application\UseCase\PlayDiceGame\PlayDiceGameCommand;
use App\Application\UseCase\PlayDiceGame\PlayDiceGameHandler;
use App\Application\UseCase\PlaySlotGame\PlaySlotGameCommand;
use App\Application\UseCase\PlaySlotGame\PlaySlotGameHandler;
use App\Domain\Common\Exceptions\InsufficientFundsException;
use App\Domain\Common\Exceptions\InvalidArgumentException;
use App\Domain\Game\Exceptions\InvalidGameTypeException;
use App\Domain\Game\GameOutcome;
use App\Domain\Game\GameType;

class GameResolver
{
    public function __construct(
        private readonly PlayDiceGameHandler $playDiceGameHandler,
        private readonly PlaySlotGameHandler $playSlotGameHandler,
    ) {
    }

    /**
     * @throws InsufficientFundsException
     * @throws InvalidArgumentException
     * @throws InvalidGameTypeException
     */
    public function resolveGame(GameType $gameType, array $validatedData, string $gameId, string $userId): GameOutcome
    {
        if ($gameType === GameType::Dice) {
            $command = PlayDiceGameCommand::fromValidated($validatedData, $gameId, $userId);
            return $this->playDiceGameHandler->handle($command);
        }

        if ($gameType === GameType::Slot) {
            $command = PlaySlotGameCommand::fromValidated($validatedData, $gameId, $userId);
            return $this->playSlotGameHandler->handle($command);
        }

        throw new InvalidGameTypeException();
    }
}
