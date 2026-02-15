<?php

namespace App\Infrastructure\Services;

use App\Domain\Exceptions\InvalidArgumentException;
use App\Domain\Games\Dice\ValueObjects\DiceNumber;
use App\Domain\Games\Services\RandomDiceNumberGenerator;
use Random\RandomException;

class PHPRandomDiceNumberGenerator implements RandomDiceNumberGenerator
{
    /**
     * @throws RandomException
     * @throws InvalidArgumentException
     */
    public function nextNumber(): DiceNumber
    {
        return new DiceNumber(random_int(DiceNumber::MIN, DiceNumber::MAX));
    }
}
