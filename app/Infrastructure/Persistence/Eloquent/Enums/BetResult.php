<?php

namespace App\Infrastructure\Persistence\Eloquent\Enums;

enum BetResult: string
{
    case Win = 'win';
    case Loss = 'loss';
}
