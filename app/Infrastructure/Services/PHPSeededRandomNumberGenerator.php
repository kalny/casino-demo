<?php

namespace App\Infrastructure\Services;

use App\Domain\Services\RandomNumberGenerator;
use Random\Engine\Mt19937;
use Random\Randomizer;

class PHPSeededRandomNumberGenerator implements RandomNumberGenerator
{
    private Randomizer $randomizer;

    public function __construct(int $seed)
    {
        $engine = new Mt19937($seed);
        $this->randomizer = new Randomizer($engine);
    }

    public function getNextRandom(int $min, int $max): int
    {
        return $this->randomizer->getInt($min, $max);
    }
}
