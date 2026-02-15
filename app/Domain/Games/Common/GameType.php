<?php

namespace App\Domain\Games\Common;

enum GameType: string
{
    case Dice = 'dice';
    case Slot = 'slot';
}
