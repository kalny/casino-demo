<?php

namespace Tests\Unit\Infrastructure\Services;

use App\Infrastructure\Services\PHPSeededRandomNumberGenerator;
use Tests\TestCase;

class PHPSeededRandomNumberGeneratorTest extends TestCase
{
    public function testGetNextRandom(): void
    {
        $rng1 = new PHPSeededRandomNumberGenerator(123);
        $rng2 = new PHPSeededRandomNumberGenerator(123);

        $sequence1 = [];
        $sequence2 = [];

        for ($i = 0; $i < 100; $i++) {
            $sequence1[] = $rng1->getNextRandom(0, 100);
            $sequence2[] = $rng2->getNextRandom(0, 100);
        }

        $this->assertEquals($sequence1, $sequence2);
    }
}
