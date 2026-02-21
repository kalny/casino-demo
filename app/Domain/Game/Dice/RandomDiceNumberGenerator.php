<?php

namespace App\Domain\Game\Dice;

use App\Domain\Common\Exceptions\InvalidArgumentException;
use App\Domain\Game\Dice\ValueObjects\DiceNumber;
use App\Domain\Common\Services\RandomNumberGenerator;

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
        return DiceNumber::fromInt($randomInt);
    }
}
