<?php

namespace Tests\Unit\Domain\Games\Dice;

use App\Domain\Exceptions\InvalidArgumentException;
use App\Domain\Games\Dice\RandomDiceNumberGenerator;
use App\Domain\Services\RandomNumberGenerator;
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
