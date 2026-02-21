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
        $diceNumber = new DiceNumber(1);

        $this->assertSame(1, $diceNumber->getValue());
    }

    public function testCreateDiceNumberFromLesserInteger(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new DiceNumber(0);
    }

    public function testCreateDiceNumberFromGreaterInteger(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new DiceNumber(7);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testEqualsCompareDiceNumberWithLesserIntegerTrue(): void
    {
        $diceNumber = new DiceNumber(4);

        $this->assertTrue($diceNumber->gt(new DiceNumber(1)));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testEqualsCompareDiceNumberWithLesserIntegerFalse(): void
    {
        $diceNumber = new DiceNumber(4);

        $this->assertFalse($diceNumber->gt(new DiceNumber(5)));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testEqualsCompareDiceNumberWithGreaterIntegerTrue(): void
    {
        $diceNumber = new DiceNumber(4);

        $this->assertTrue($diceNumber->lt(new DiceNumber(6)));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testEqualsCompareDiceNumberWithGreaterIntegerFalse(): void
    {
        $diceNumber = new DiceNumber(4);

        $this->assertFalse($diceNumber->lt(new DiceNumber(3)));
    }
}
