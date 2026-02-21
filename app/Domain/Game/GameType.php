<?php

namespace App\Domain\Game;

enum GameType: string
{
    case Dice = 'dice';
    case Slot = 'slot';
}
