<?php

namespace Tests\Unit\Domain\Game\Dice;

use App\Domain\Common\Exceptions\InvalidArgumentException;
use App\Domain\Game\Dice\RandomDiceNumberGenerator;
use App\Domain\Common\Services\RandomNumberGenerator;
use Tests\TestCase;

class RandomDiceNumberGeneratorTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     */
    public function testNextNumber(): void
    {
        $rng = $this->createMock(RandomNumberGenerator::class);
        $rng
            ->expects($this->once())
            ->method('getNextRandom')
            ->willReturn(5);

        $randomDiceNumberGenerator = new RandomDiceNumberGenerator($rng);
        $diceNumber = $randomDiceNumberGenerator->nextNumber();

        $this->assertSame(5, $diceNumber->getValue());
    }
}
