<?php

namespace Tests\Unit\Domain\Common;

use App\Domain\Common\ValueObjects\BetMultiplier;
use App\Domain\Exceptions\InvalidArgumentException;
use Tests\TestCase;

class BetMultiplierTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     */
    public function testCreateValidBetMultiplierFromInteger(): void
    {
        $betMultiplier = new BetMultiplier(5);

        $this->assertSame(5, $betMultiplier->getValue());
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testCompareBetMultiplierWithLesser(): void
    {
        $betMultiplier = new BetMultiplier(5);
        $otherBetMultiplier = new BetMultiplier(4);

        $this->assertTrue($betMultiplier->gt($otherBetMultiplier));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testCompareBetMultiplierWithGreater(): void
    {
        $betMultiplier = new BetMultiplier(5);
        $otherBetMultiplier = new BetMultiplier(6);

        $this->assertFalse($betMultiplier->gt($otherBetMultiplier));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testAddValueFromOtherMultiplier(): void
    {
        $betMultiplier = new BetMultiplier(5);
        $otherBetMultiplier = new BetMultiplier(5);

        $this->assertSame(10, $betMultiplier->add($otherBetMultiplier)->getValue());
        $this->assertNotSame($betMultiplier, $betMultiplier->add($otherBetMultiplier));
    }

    public function testCreateWinAmountWithNegativeValue(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new BetMultiplier(-1);
    }
}
