<?php

namespace App\Domain\Games\Slot\ValueObjects;

use App\Domain\Common\ValueObjects\BetMultiplier;
use App\Domain\Exceptions\InvalidArgumentException;
use App\Domain\Games\Slot\PaylinesFormatChecker;

final readonly class WinningPaylines
{
    /**
     * @throws InvalidArgumentException
     */
    public function __construct(private array $paylines, private BetMultiplier $betMultiplier)
    {
        if (!empty($this->paylines)) {
            $paylinesFormatChecker = new PaylinesFormatChecker();
            $paylinesFormatChecker->check($this->paylines);
        }
    }

    public function getData(): array
    {
        return $this->paylines;
    }

    public function getMultiplier(): BetMultiplier
    {
        return $this->betMultiplier;
    }

    public function isEmpty(): bool
    {
        return empty($this->paylines);
    }
}
