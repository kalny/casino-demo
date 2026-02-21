<?php

namespace App\Domain\Game;

enum OutcomeStatus: string
{
    case Win = 'win';
    case Loss = 'loss';
}
