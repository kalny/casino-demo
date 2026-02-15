<?php

namespace Tests\Unit\Domain\Games\Slot\ValueObjects;

use App\Domain\Exceptions\InvalidArgumentException;
use App\Domain\Games\Slot\ValueObjects\GridInt;
use Tests\TestCase;

class GridIntTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     */
    public function testCreateValidGridIntFromInteger(): void
    {
        $gridInt = new GridInt(3);

        $this->assertSame(3, $gridInt->getValue());
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testCreateGridIntFromTooSmallValue(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new GridInt(1);
    }
}
