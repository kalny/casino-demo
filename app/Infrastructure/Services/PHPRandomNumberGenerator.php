<?php

namespace App\Infrastructure\Services;

use App\Domain\Common\Services\RandomNumberGenerator;
use Random\RandomException;

class PHPRandomNumberGenerator implements RandomNumberGenerator
{
    /**
     * @throws RandomException
     */
    public function getNextRandom(int $min, int $max): int
    {
        return random_int($min, $max);
    }
}
