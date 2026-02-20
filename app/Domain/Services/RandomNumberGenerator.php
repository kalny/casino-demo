<?php

namespace App\Domain\Services;

interface RandomNumberGenerator
{
    public function getNextRandom(int $min, int $max): int;
}
