<?php

namespace App\Domain\Games\Dice\ValueObjects;

enum PlayDiceType: string
{
    case Over = 'over';
    case Under = 'under';
}
