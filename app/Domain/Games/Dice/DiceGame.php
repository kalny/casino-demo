<?php

namespace App\Domain\Games\Dice;

use App\Domain\Exceptions\InvalidArgumentException;
use App\Domain\Games\Common\GameOutcome;
use App\Domain\Games\Common\GameType;
use App\Domain\Games\Common\OutcomeStatus;
use App\Domain\Games\Dice\ValueObjects\PlayDiceInput;
use App\Domain\Games\Game;
use App\Domain\Games\GameId;
use App\Domain\Common\ValueObjects\BetMultiplier;
use App\Domain\Common\ValueObjects\WinAmount;
use Random\RandomException;

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
