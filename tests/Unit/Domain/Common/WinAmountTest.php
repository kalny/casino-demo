<?php

namespace Tests\Unit\Domain\Common;

use App\Domain\Common\ValueObjects\WinAmount;
use App\Domain\Exceptions\InvalidArgumentException;
use Tests\TestCase;

class WinAmountTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     */
    public function testCreateValidWinAmountFromInteger(): void
    {
        $winAmount = new WinAmount(100);

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

        new WinAmount(-1);
    }
}
