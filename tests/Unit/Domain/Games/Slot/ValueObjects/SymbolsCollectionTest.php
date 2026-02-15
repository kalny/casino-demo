<?php

namespace Tests\Unit\Domain\Games\Slot\ValueObjects;

use App\Domain\Common\ValueObjects\BetMultiplier;
use App\Domain\Exceptions\InvalidArgumentException;
use App\Domain\Games\Slot\ValueObjects\Symbol;
use App\Domain\Games\Slot\ValueObjects\SymbolName;
use App\Domain\Games\Slot\ValueObjects\SymbolsCollection;
use Tests\TestCase;

class SymbolsCollectionTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     */
    public function testCreateValidSymbolsCollectionFromArrayOfStrings(): void
    {
        $symbolsCollection = new SymbolsCollection([
            ['name' => 'A', 'multiplier' => 5],
            ['name' => 'B', 'multiplier' => 6],
            ['name' => 'C', 'multiplier' => 7],
        ]);

        $this->assertSame(3, count($symbolsCollection->getData()));
        $this->assertInstanceOf(Symbol::class, $symbolsCollection->getData()[0]);
        $this->assertInstanceOf(Symbol::class, $symbolsCollection->getData()[1]);
        $this->assertInstanceOf(Symbol::class, $symbolsCollection->getData()[2]);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testCreateValidSymbolsCollectionFromArrayOfObject(): void
    {
        $symbolsCollection = new SymbolsCollection([
            new Symbol(new SymbolName('A'), new BetMultiplier(5)),
            new Symbol(new SymbolName('B'), new BetMultiplier(6)),
            new Symbol(new SymbolName('C'), new BetMultiplier(7)),
        ]);

        $this->assertSame(3, count($symbolsCollection->getData()));
        $this->assertInstanceOf(Symbol::class, $symbolsCollection->getData()[0]);
        $this->assertInstanceOf(Symbol::class, $symbolsCollection->getData()[1]);
        $this->assertInstanceOf(Symbol::class, $symbolsCollection->getData()[2]);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testCreateValidSymbolsCollectionFromMixedArray(): void
    {
        $symbolsCollection = new SymbolsCollection([
            new Symbol(new SymbolName('A'), new BetMultiplier(5)),
            new Symbol(new SymbolName('B'), new BetMultiplier(6)),
            ['name' => 'C', 'multiplier' => 7],
            ['name' => 'D', 'multiplier' => 8],
        ]);

        $this->assertSame(4, count($symbolsCollection->getData()));
        $this->assertInstanceOf(Symbol::class, $symbolsCollection->getData()[0]);
        $this->assertInstanceOf(Symbol::class, $symbolsCollection->getData()[1]);
        $this->assertInstanceOf(Symbol::class, $symbolsCollection->getData()[2]);
        $this->assertInstanceOf(Symbol::class, $symbolsCollection->getData()[3]);
    }

    public function testCreateSymbolsCollectionFromArrayWithMissedName(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new SymbolsCollection([
            ['multiplier' => 5],
            ['name' => 'B', 'multiplier' => 6],
            ['name' => 'C', 'multiplier' => 7],
        ]);
    }

    public function testCreateSymbolsCollectionFromArrayWithMissedMultiplier(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new SymbolsCollection([
            ['name' => 'A'],
            ['name' => 'B', 'multiplier' => 6],
            ['name' => 'C', 'multiplier' => 7],
        ]);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testGetSymbol(): void
    {
        $symbolsCollection = new SymbolsCollection([
            new Symbol(new SymbolName('A'), new BetMultiplier(5)),
            new Symbol(new SymbolName('B'), new BetMultiplier(6)),
            ['name' => 'C', 'multiplier' => 7],
            ['name' => 'D', 'multiplier' => 8],
        ]);

        $symbol = $symbolsCollection->getSymbol('C');

        $this->assertSame('C', $symbol->name->getValue());
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testGetNonExistentSymbol(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $symbolsCollection = new SymbolsCollection([
            new Symbol(new SymbolName('A'), new BetMultiplier(5)),
            new Symbol(new SymbolName('B'), new BetMultiplier(6)),
            ['name' => 'C', 'multiplier' => 7],
            ['name' => 'D', 'multiplier' => 8],
        ]);

        $symbolsCollection->getSymbol('E');
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testIsWinningTrue(): void
    {
        $symbolsCollection = new SymbolsCollection([
            new Symbol(new SymbolName('A'), new BetMultiplier(5)),
            new Symbol(new SymbolName('A'), new BetMultiplier(5)),
            new Symbol(new SymbolName('A'), new BetMultiplier(5)),
        ]);

        $this->assertTrue($symbolsCollection->isWinning());
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testIsWinningFalse(): void
    {
        $symbolsCollection = new SymbolsCollection([
            new Symbol(new SymbolName('A'), new BetMultiplier(5)),
            new Symbol(new SymbolName('B'), new BetMultiplier(6)),
            new Symbol(new SymbolName('A'), new BetMultiplier(5)),
        ]);

        $this->assertFalse($symbolsCollection->isWinning());
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testGetMaxMultiplier(): void
    {
        $symbolsCollection = new SymbolsCollection([
            new Symbol(new SymbolName('A'), new BetMultiplier(5)),
            new Symbol(new SymbolName('B'), new BetMultiplier(6)),
            new Symbol(new SymbolName('C'), new BetMultiplier(7)),
        ]);

        $maxMultiplier = $symbolsCollection->getMaxMultiplier();

        $this->assertSame(7, $maxMultiplier->getValue());
    }
}
