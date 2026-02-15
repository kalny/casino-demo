<?php

namespace Tests\Unit\Domain\Common;

use App\Domain\Common\ValueObjects\BetAmount;
use App\Domain\Common\ValueObjects\BetMultiplier;
use App\Domain\Exceptions\InvalidArgumentException;
use Tests\TestCase;

class BetAmountTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     */
    public function testCreateValidBetAmountFromInteger(): void
    {
        $betAmount = new BetAmount(100);

        $this->assertSame(100, $betAmount->getValue());
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testMultiplyBetAmount(): void
    {
        $betMultiplier = new BetMultiplier(5);
        $betAmount = new BetAmount(100);

        $winAmount = $betAmount->multiply($betMultiplier);
        $this->assertSame(500, $winAmount->getValue());
    }

    public function testCreateBetAmountWithZeroValue(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new BetAmount(0);
    }

    public function testCreateBetAmountWithNegativeValue(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new BetAmount(-1);
    }
}
