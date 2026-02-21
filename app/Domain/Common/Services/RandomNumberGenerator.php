<?php

namespace App\Domain\Common\Services;

interface RandomNumberGenerator
{
    public function getNextRandom(int $min, int $max): int;
}
