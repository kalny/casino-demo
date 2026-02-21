<?php

namespace App\Domain\Game\Slot;

use App\Domain\Common\ValueObjects\BetMultiplier;
use App\Domain\Game\GameSpecificOutcome;
use App\Domain\Game\Slot\ValueObjects\Grid;
use App\Domain\Game\Slot\ValueObjects\WinningPaylines;

final readonly class SlotSpecificOutcome extends GameSpecificOutcome
{
    public function __construct(
        public BetMultiplier $multiplier,
        public Grid $grid,
        public WinningPaylines $winningPaylines,
    ) {
    }

    public function jsonSerialize(): mixed
    {
        return [
            'multiplier' => $this->multiplier->getValue(),
            'grid' => $this->renderGrid(),
            'winning_paylines' => $this->winningPaylines->getData(),
        ];
    }

    private function renderGrid(): array
    {
        $renderedGrid = [];
        foreach ($this->grid->getData() as $reel) {
            $symbols = [];
            foreach ($reel->getData() as $symbol) {
                $symbols[] = $symbol->name->getValue();
            }
            $renderedGrid[] = $symbols;
        }
        return $renderedGrid;
    }
}
