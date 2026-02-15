<?php

namespace App\Domain\Games\Slot\ValueObjects;

use App\Domain\Exceptions\InvalidArgumentException;
use App\Domain\Common\ValueObjects\BetMultiplier;

final readonly class SymbolsCollection
{
    /** @var Symbol[] $reelStrip */
    private array $symbols;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(array $symbolsArray)
    {
        $symbols = [];

        foreach ($symbolsArray as $symbol) {
            if ($symbol instanceof Symbol) {
                $symbols[] = $symbol;
                continue;
            }

            if (!isset($symbol['name'])) {
                throw new InvalidArgumentException("Symbol name is required");
            }

            if (!isset($symbol['multiplier'])) {
                throw new InvalidArgumentException("Symbol multiplier is required");
            }

            $symbols[] = new Symbol(
                name: new SymbolName($symbol['name']),
                multiplier: new BetMultiplier($symbol['multiplier'])
            );
        }

        $this->symbols = $symbols;
    }

    public function getData(): array
    {
        return $this->symbols;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getSymbol(string $symbolName): Symbol
    {
        $result = array_filter($this->symbols, fn (Symbol $symbol) => $symbol->name->equals($symbolName));
        if (!$result) {
            throw new InvalidArgumentException("Symbol $symbolName does not exist");
        }
        return reset($result);
    }

    public function isWinning(): bool
    {
        $symbolsArray = [];
        foreach ($this->symbols as $symbol) {
            $symbolsArray[] = $symbol->name->getValue();
        }
        return count(array_unique($symbolsArray)) === 1;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getMaxMultiplier(): BetMultiplier
    {
        $maxMultiplier = new BetMultiplier(0);

        foreach ($this->symbols as $symbol) {
            if ($symbol->multiplier->gt($maxMultiplier)) {
                $maxMultiplier = $symbol->multiplier;
            }
        }

        return $maxMultiplier;
    }
}
