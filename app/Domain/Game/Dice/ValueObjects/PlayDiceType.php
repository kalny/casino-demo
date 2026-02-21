<?php

namespace App\Domain\Game\Dice\ValueObjects;

enum PlayDiceType: string
{
    case Over = 'over';
    case Under = 'under';
}
