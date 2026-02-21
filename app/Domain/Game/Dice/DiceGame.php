<?php

namespace App\Domain\Game\Dice;

use App\Domain\Common\Exceptions\InvalidArgumentException;
use App\Domain\Game\GameOutcome;
use App\Domain\Game\GameType;
use App\Domain\Game\OutcomeStatus;
use App\Domain\Game\Dice\ValueObjects\PlayDiceInput;
use App\Domain\Game\Game;
use App\Domain\Game\GameId;
use App\Domain\Common\ValueObjects\BetMultiplier;
use App\Domain\Common\ValueObjects\WinAmount;

final class DiceGame extends Game
{
    public function __construct(
        GameId $gameId,
        string $name,
        private readonly BetMultiplier $multiplier
    ) {
        parent::__construct($gameId, GameType::Dice, $name);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function playDice(
        PlayDiceInput $playInput,
        RandomDiceNumberGenerator $randomDiceNumberGenerator
    ): GameOutcome {
        $chosenNumber = $playInput->chosenNumber;

        $roll = $randomDiceNumberGenerator->nextNumber();

        $win = $playInput->isOver()
            ? $chosenNumber->lt($roll)
            : $chosenNumber->gt($roll);

        $betAmount = $playInput->betAmount;

        $winAmount = $win
            ? $betAmount->multiply($this->multiplier)
            : WinAmount::zero();

        return new GameOutcome(
            gameId: $this->getId(),
            userId: $playInput->userId,
            betAmount: $betAmount,
            winAmount: $winAmount,
            outcomeStatus: $win
                ? OutcomeStatus::Win
                : OutcomeStatus::Loss,
            gameSpecificOutcome: new DiceSpecificOutcome(
                multiplier: $this->multiplier,
                roll: $roll
            )
        );
    }
}
