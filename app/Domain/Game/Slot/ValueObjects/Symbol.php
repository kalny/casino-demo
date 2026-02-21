<?php

namespace App\Domain\Game\Slot\ValueObjects;

use App\Domain\Common\ValueObjects\BetMultiplier;

final readonly class Symbol
{
    public function __construct(
        public SymbolName $name,
        public BetMultiplier $multiplier
    ) {
    }
}
