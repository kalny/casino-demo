<?php

namespace Tests\Unit\Domain\Common\ValueObjects;

use App\Domain\Common\ValueObjects\BetAmount;
use App\Domain\Common\ValueObjects\BetMultiplier;
use App\Domain\Common\Exceptions\InvalidArgumentException;
use Tests\TestCase;

class BetAmountTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     */
    public function testCreateValidBetAmountFromInteger(): void
    {
        $betAmount = BetAmount::fromInt(100);

        $this->assertSame(100, $betAmount->getValue());
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testMultiplyBetAmount(): void
    {
        $betMultiplier = BetMultiplier::fromInt(5);
        $betAmount = BetAmount::fromInt(100);

        $winAmount = $betAmount->multiply($betMultiplier);
        $this->assertSame(500, $winAmount->getValue());
    }

    public function testCreateBetAmountWithZeroValue(): void
    {
        $this->expectException(InvalidArgumentException::class);

        BetAmount::fromInt(0);
    }

    public function testCreateBetAmountWithNegativeValue(): void
    {
        $this->expectException(InvalidArgumentException::class);

        BetAmount::fromInt(-1);
    }
}
