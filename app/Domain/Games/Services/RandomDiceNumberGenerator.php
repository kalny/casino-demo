<?php

namespace App\Domain\Games\Services;

use App\Domain\Games\Dice\ValueObjects\DiceNumber;

interface RandomDiceNumberGenerator
{
    public function nextNumber(): DiceNumber;
}
