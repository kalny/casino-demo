<?php

namespace Tests\Unit\Domain\Game\Slot\ValueObjects;

use App\Domain\Common\Exceptions\InvalidArgumentException;
use App\Domain\Game\Slot\ValueObjects\Paylines;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PaylinesTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     */
    public function testCreateValidPaylinesFromArray(): void
    {
        $paylinesArray = [
            [[0, 1], [0, 2], [0, 3]],
        ];

        $paylines = Paylines::fromArray($paylinesArray);

        $this->assertSame($paylinesArray, $paylines->getData());
    }

    public function testCreatePaylinesFromEmptyArray(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Paylines::fromArray([]);
    }

    #[dataProvider('invalidArraysDataProvider')]
    public function testCreatePaylinesFromInvalidArray(array $array): void
    {
        $this->expectException(InvalidArgumentException::class);

        Paylines::fromArray($array);
    }

    public static function invalidArraysDataProvider(): array
    {
        return [
            ['array' => [[1, 2, 3]]],
            ['array' => [[[1], [2], [3]]]],
            ['array' => [[['wrong'], ['wrong'], ['wrong']]]],
            ['array' => [[[1, 'wrong'], [2, 'wrong'], [3, 'wrong']]]],
        ];
    }
}
