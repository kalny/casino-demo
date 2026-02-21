<?php

namespace App\Domain\Game\Slot\ValueObjects;

use App\Domain\Common\Exceptions\InvalidArgumentException;

final readonly class ReelStrip
{
    private const MIN_LENGTH = 3;

    /** @var Symbol[] $reelStrip */
    private array $reelStrip;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(array $reelStrip, private SymbolsCollection $symbols)
    {
        if (count($reelStrip) < self::MIN_LENGTH) {
            throw new InvalidArgumentException('The minimum reelStrip length has not been reached');
        }

        $reelStripSymbols = [];

        foreach ($reelStrip as $reelStripSymbol) {
            $reelStripSymbols[] = $this->symbols->getSymbol($reelStripSymbol);
        }

        $this->reelStrip = $reelStripSymbols;
    }

    public function getData(): array
    {
        return $this->reelStrip;
    }
}
