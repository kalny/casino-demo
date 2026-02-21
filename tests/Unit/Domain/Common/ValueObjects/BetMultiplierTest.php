<?php

namespace Tests\Unit\Domain\Common\ValueObjects;

use App\Domain\Common\ValueObjects\BetMultiplier;
use App\Domain\Common\Exceptions\InvalidArgumentException;
use Tests\TestCase;

class BetMultiplierTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     */
    public function testCreateValidBetMultiplierFromInteger(): void
    {
        $betMultiplier = BetMultiplier::fromInt(5);

        $this->assertSame(5, $betMultiplier->getValue());
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testCompareBetMultiplierWithLesser(): void
    {
        $betMultiplier = BetMultiplier::fromInt(5);
        $otherBetMultiplier = BetMultiplier::fromInt(4);

        $this->assertTrue($betMultiplier->gt($otherBetMultiplier));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testCompareBetMultiplierWithGreater(): void
    {
        $betMultiplier = BetMultiplier::fromInt(5);
        $otherBetMultiplier = BetMultiplier::fromInt(6);

        $this->assertFalse($betMultiplier->gt($otherBetMultiplier));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testAddValueFromOtherMultiplier(): void
    {
        $betMultiplier = BetMultiplier::fromInt(5);
        $otherBetMultiplier = BetMultiplier::fromInt(5);

        $this->assertSame(10, $betMultiplier->add($otherBetMultiplier)->getValue());
        $this->assertNotSame($betMultiplier, $betMultiplier->add($otherBetMultiplier));
    }

    public function testCreateWinAmountWithNegativeValue(): void
    {
        $this->expectException(InvalidArgumentException::class);

        BetMultiplier::fromInt(-1);
    }
}
