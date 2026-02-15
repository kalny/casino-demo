<?php

namespace App\Domain\Games\Slot;

use App\Domain\Exceptions\InvalidArgumentException;
use App\Domain\Games\Common\GameOutcome;
use App\Domain\Games\Common\GameType;
use App\Domain\Games\Common\OutcomeStatus;
use App\Domain\Games\Services\RandomGridGenerator;
use App\Domain\Games\Game;
use App\Domain\Games\GameId;
use App\Domain\Games\Slot\ValueObjects\GridInt;
use App\Domain\Games\Slot\ValueObjects\Paylines;
use App\Domain\Games\Slot\ValueObjects\PlaySlotInput;
use App\Domain\Games\Slot\ValueObjects\ReelStrip;
use App\Domain\Common\ValueObjects\WinAmount;

final class SlotGame extends Game
{
    public function __construct(
        GameId $gameId,
        string $name,
        private readonly GridInt $reelsNumber,
        private readonly GridInt $symbolsNumber,
        private readonly ReelStrip $reelStrip,
        private readonly Paylines $paylines,
    ) {
        parent::__construct($gameId, GameType::Slot, $name);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function playSlot(PlaySlotInput $playInput, RandomGridGenerator $rgg): GameOutcome
    {
        $grid = $rgg->nextGrid($this->reelsNumber, $this->symbolsNumber, $this->reelStrip);

        $winningPaylines = $grid->getWinningPaylines($this->paylines);

        $betMultiplier = $winningPaylines->getMultiplier();

        $win = !$winningPaylines->isEmpty();

        $betAmount = $playInput->betAmount;

        $winAmount = $win ? $betAmount->multiply($betMultiplier): WinAmount::zero();

        return new GameOutcome(
            gameId: $this->getId(),
            userId: $playInput->userId,
            betAmount: $betAmount,
            winAmount: $winAmount,
            outcomeStatus: $win
                ? OutcomeStatus::Win
                : OutcomeStatus::Loss,
            gameSpecificOutcome: new SlotSpecificOutcome(
                multiplier: $betMultiplier,
                grid: $grid,
                winningPaylines: $winningPaylines
            )
        );
    }
}
