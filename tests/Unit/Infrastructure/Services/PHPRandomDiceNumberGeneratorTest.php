<?php

namespace Tests\Unit\Infrastructure\Services;

use App\Domain\Exceptions\InvalidArgumentException;
use App\Infrastructure\Services\PHPRandomDiceNumberGenerator;
use Random\RandomException;
use Tests\TestCase;

class PHPRandomDiceNumberGeneratorTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     * @throws RandomException
     */
    public function testNextNumber(): void
    {
        $rng = new PHPRandomDiceNumberGenerator();
        $diceNumber = $rng->nextNumber();

        $this->assertLessThanOrEqual(6, $diceNumber->getValue());
        $this->assertGreaterThanOrEqual(1, $diceNumber->getValue());
    }
}
