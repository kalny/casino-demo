<?php

namespace App\Domain\Games\Slot\ValueObjects;

use App\Domain\Common\ValueObjects\BetMultiplier;

final readonly class Symbol
{
    public function __construct(
        public SymbolName $name,
        public BetMultiplier $multiplier
    ) {
    }
}
