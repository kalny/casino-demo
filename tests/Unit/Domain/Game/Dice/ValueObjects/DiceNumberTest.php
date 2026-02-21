<?php

namespace Tests\Unit\Domain\Game\Dice\ValueObjects;

use App\Domain\Common\Exceptions\InvalidArgumentException;
use App\Domain\Game\Dice\ValueObjects\DiceNumber;
use Tests\TestCase;

class DiceNumberTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     */
    public function testCreateValidDiceNumberFromInteger(): void
    {
        $diceNumber = DiceNumber::fromInt(1);

        $this->assertSame(1, $diceNumber->getValue());
    }

    public function testCreateDiceNumberFromLesserInteger(): void
    {
        $this->expectException(InvalidArgumentException::class);

        DiceNumber::fromInt(0);
    }

    public function testCreateDiceNumberFromGreaterInteger(): void
    {
        $this->expectException(InvalidArgumentException::class);

        DiceNumber::fromInt(7);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testEqualsCompareDiceNumberWithLesserIntegerTrue(): void
    {
        $diceNumber = DiceNumber::fromInt(4);

        $this->assertTrue($diceNumber->gt(DiceNumber::fromInt(1)));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testEqualsCompareDiceNumberWithLesserIntegerFalse(): void
    {
        $diceNumber = DiceNumber::fromInt(4);

        $this->assertFalse($diceNumber->gt(DiceNumber::fromInt(5)));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testEqualsCompareDiceNumberWithGreaterIntegerTrue(): void
    {
        $diceNumber = DiceNumber::fromInt(4);

        $this->assertTrue($diceNumber->lt(DiceNumber::fromInt(6)));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testEqualsCompareDiceNumberWithGreaterIntegerFalse(): void
    {
        $diceNumber = DiceNumber::fromInt(4);

        $this->assertFalse($diceNumber->lt(DiceNumber::fromInt(3)));
    }
}
