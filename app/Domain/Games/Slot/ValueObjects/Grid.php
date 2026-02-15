<?php

namespace App\Domain\Games\Slot\ValueObjects;

use App\Domain\Exceptions\InvalidArgumentException;
use App\Domain\Common\ValueObjects\BetMultiplier;

final readonly class Grid
{
    /** @var SymbolsCollection[] $grid */
    private array $grid;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(array $gridArray)
    {
        $reels = [];
        foreach ($gridArray as $reel) {
            $reels[] = new SymbolsCollection($reel);
        }
        $this->grid = $reels;
    }

    public function getData(): array
    {
        return $this->grid;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getWinningPaylines(Paylines $paylines): WinningPaylines
    {
        $winningPaylinesArray = [];
        $betMultiplier = new BetMultiplier(0);

        foreach ($paylines->getData() as $payline) {

            $paylineSymbols = [];

            foreach ($payline as $paylineItem) {
                $reelNumber = $paylineItem[0];
                $symbolNumber = $paylineItem[1];

                $reelCollection = $this->grid[$reelNumber];
                if (!isset($reelCollection->getData()[$symbolNumber])) {
                    throw new InvalidArgumentException("Invalid symbol $symbolNumber");
                }
                $paylineSymbols[] = $reelCollection->getData()[$symbolNumber];
            }

            $reelCollection = new SymbolsCollection($paylineSymbols);

            if ($reelCollection->isWinning()) {
                $winningPaylinesArray[] = $payline;
                $maxMultiplier = $reelCollection->getMaxMultiplier();

                $betMultiplier = $betMultiplier->add($maxMultiplier);
            }
        }

        return new WinningPaylines($winningPaylinesArray, $betMultiplier);
    }
}
