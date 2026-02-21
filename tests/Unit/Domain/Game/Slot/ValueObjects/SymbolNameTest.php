<?php

namespace Tests\Unit\Domain\Game\Slot\ValueObjects;

use App\Domain\Common\Exceptions\InvalidArgumentException;
use App\Domain\Game\Slot\ValueObjects\SymbolName;
use Tests\TestCase;

class SymbolNameTest extends TestCase
{
    public function testCreateValidSymbolNameFromString(): void
    {
        $symbolName = new SymbolName('A');

        $this->assertSame('A', $symbolName->getValue());
    }

    public function testEqualsCompareSymbolNameWithStringTrue(): void
    {
        $symbolName = new SymbolName('A');

        $this->assertTrue($symbolName->equals('A'));
    }

    public function testEqualsCompareSymbolNameWithStringFalse(): void
    {
        $symbolName = new SymbolName('A');

        $this->assertFalse($symbolName->equals('B'));
    }

    public function testCreateSymbolNameWithEmptyString(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new SymbolName('');
    }
}
