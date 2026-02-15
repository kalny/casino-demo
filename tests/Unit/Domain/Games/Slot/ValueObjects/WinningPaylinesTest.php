<?php

namespace Tests\Unit\Domain\Games\Slot\ValueObjects;

use App\Domain\Common\ValueObjects\BetMultiplier;
use App\Domain\Exceptions\InvalidArgumentException;
use App\Domain\Games\Slot\ValueObjects\WinningPaylines;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class WinningPaylinesTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     */
    public function testCreateValidWinningPaylinesFromArray(): void
    {
        $paylinesArray = [
            [[0, 1], [0, 2], [0, 3]],
        ];

        $paylines = new WinningPaylines($paylinesArray, new BetMultiplier(5));

        $this->assertSame($paylinesArray, $paylines->getData());
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testCreateValidWinningPaylinesFromEmptyArray(): void
    {
        $paylinesArray = [];

        $paylines = new WinningPaylines($paylinesArray, new BetMultiplier(5));

        $this->assertSame($paylinesArray, $paylines->getData());
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testIsEmpty(): void
    {
        $paylinesArray = [];

        $paylines = new WinningPaylines($paylinesArray, new BetMultiplier(5));

        $this->assertTrue($paylines->isEmpty());
    }

    #[dataProvider('invalidArraysDataProvider')]
    public function testCreatePaylinesFromInvalidArray(array $array): void
    {
        $this->expectException(InvalidArgumentException::class);

        new WinningPaylines($array, new BetMultiplier(5));
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
