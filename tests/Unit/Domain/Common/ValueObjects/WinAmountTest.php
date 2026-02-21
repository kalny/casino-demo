<?php

namespace Tests\Unit\Domain\Common\ValueObjects;

use App\Domain\Common\ValueObjects\WinAmount;
use App\Domain\Common\Exceptions\InvalidArgumentException;
use Tests\TestCase;

class WinAmountTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     */
    public function testCreateValidWinAmountFromInteger(): void
    {
        $winAmount = WinAmount::fromInt(100);

        $this->assertSame(100, $winAmount->getValue());
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testCreateZeroWinAmount(): void
    {
        $winAmount = WinAmount::zero();

        $this->assertSame(0, $winAmount->getValue());
    }

    public function testCreateWinAmountWithNegativeValue(): void
    {
        $this->expectException(InvalidArgumentException::class);

        WinAmount::fromInt(-1);
    }
}
