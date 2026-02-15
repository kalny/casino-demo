<?php

namespace App\Domain\Games\Common;

enum OutcomeStatus: string
{
    case Win = 'win';
    case Loss = 'loss';
}
