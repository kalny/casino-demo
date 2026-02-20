<?php

namespace Tests\Unit\Infrastructure\Services;

use App\Infrastructure\Services\PHPRandomNumberGenerator;
use Random\RandomException;
use Tests\TestCase;

class PHPRandomNumberGeneratorTest extends TestCase
{
    /**
     * @throws RandomException
     */
    public function testGetNextRandom(): void
    {
        $generator = new PHPRandomNumberGenerator();
        $randomNumber = $generator->getNextRandom(3, 6);

        $this->assertGreaterThanOrEqual(3, $randomNumber);
        $this->assertLessThanOrEqual(6, $randomNumber);
    }
}
