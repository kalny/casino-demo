<?php

namespace App\Domain\Games\Dice;

use App\Domain\Exceptions\InvalidArgumentException;
use App\Domain\Games\Dice\ValueObjects\DiceNumber;
use App\Domain\Services\RandomNumberGenerator;

class RandomDiceNumberGenerator
{
    public function __construct(private readonly RandomNumberGenerator $generator)
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function nextNumber(): DiceNumber
    {
        $randomInt = $this->generator->getNextRandom(DiceNumber::MIN, DiceNumber::MAX);
        return new DiceNumber($randomInt);
    }
}
