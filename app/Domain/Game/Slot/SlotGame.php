<?php

namespace App\Domain\Game\Slot;

use App\Domain\Common\Exceptions\InvalidArgumentException;
use App\Domain\Game\GameOutcome;
use App\Domain\Game\GameType;
use App\Domain\Game\OutcomeStatus;
use App\Domain\Game\Game;
use App\Domain\Game\GameId;
use App\Domain\Game\Slot\ValueObjects\GridInt;
use App\Domain\Game\Slot\ValueObjects\Paylines;
use App\Domain\Game\Slot\ValueObjects\PlaySlotInput;
use App\Domain\Game\Slot\ValueObjects\ReelStrip;
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
    public function playSlot(
        PlaySlotInput $playInput,
        RandomGridGenerator $randomGridGenerator
    ): GameOutcome {
        $grid = $randomGridGenerator->nextGrid($this->reelsNumber, $this->symbolsNumber, $this->reelStrip);

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
