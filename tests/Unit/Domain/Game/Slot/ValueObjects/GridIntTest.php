<?php

namespace Tests\Unit\Domain\Game\Slot\ValueObjects;

use App\Domain\Common\Exceptions\InvalidArgumentException;
use App\Domain\Game\Slot\ValueObjects\GridInt;
use Tests\TestCase;

class GridIntTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     */
    public function testCreateValidGridIntFromInteger(): void
    {
        $gridInt = GridInt::fromInt(3);

        $this->assertSame(3, $gridInt->getValue());
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testCreateGridIntFromTooSmallValue(): void
    {
        $this->expectException(InvalidArgumentException::class);

        GridInt::fromInt(1);
    }
}
