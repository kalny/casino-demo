<?php

namespace Tests\Unit\Domain\Game\Slot\ValueObjects;

use App\Domain\Common\Exceptions\InvalidArgumentException;
use App\Domain\Game\Slot\ValueObjects\Grid;
use App\Domain\Game\Slot\ValueObjects\Paylines;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class GridTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     */
    public function testCreateValidGridFromArray(): void
    {
        $grid = new Grid([
            [
                ['name' => 'A', 'multiplier' => 5],
                ['name' => 'B', 'multiplier' => 6],
                ['name' => 'C', 'multiplier' => 7],
            ],
            [
                ['name' => 'A', 'multiplier' => 5],
                ['name' => 'B', 'multiplier' => 6],
                ['name' => 'C', 'multiplier' => 7],
            ],
            [
                ['name' => 'A', 'multiplier' => 5],
                ['name' => 'B', 'multiplier' => 6],
                ['name' => 'C', 'multiplier' => 7],
            ]
        ]);

        $this->assertSame(3, count($grid->getData()));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testGetWinningPaylinesEmpty(): void
    {
        $grid = new Grid([
            [
                ['name' => 'A', 'multiplier' => 5],
                ['name' => 'B', 'multiplier' => 6],
                ['name' => 'C', 'multiplier' => 7],
            ],
            [
                ['name' => 'A', 'multiplier' => 5],
                ['name' => 'B', 'multiplier' => 6],
                ['name' => 'C', 'multiplier' => 7],
            ],
            [
                ['name' => 'A', 'multiplier' => 5],
                ['name' => 'B', 'multiplier' => 6],
                ['name' => 'C', 'multiplier' => 7],
            ]
        ]);

        $paylines = Paylines::fromArray([
            [[0, 0], [1, 1], [2, 2]],
        ]);

        $winningPaylines = $grid->getWinningPaylines($paylines);
        $this->assertSame(0, count($winningPaylines->getData()));
    }

    /**
     * @throws InvalidArgumentException
     */
    #[dataProvider('winningPaylinesDataProvider')]
    public function testGetWinningPaylinesNotEmpty(array $paylinesArray, int $winningPaylinesCount): void
    {
        $grid = new Grid([
            [
                ['name' => 'A', 'multiplier' => 5],
                ['name' => 'B', 'multiplier' => 6],
                ['name' => 'C', 'multiplier' => 7],
            ],
            [
                ['name' => 'A', 'multiplier' => 5],
                ['name' => 'B', 'multiplier' => 6],
                ['name' => 'C', 'multiplier' => 7],
            ],
            [
                ['name' => 'A', 'multiplier' => 5],
                ['name' => 'B', 'multiplier' => 6],
                ['name' => 'C', 'multiplier' => 7],
            ]
        ]);

        $paylines = Paylines::fromArray($paylinesArray);

        $winningPaylines = $grid->getWinningPaylines($paylines);
        $this->assertSame($winningPaylinesCount, count($winningPaylines->getData()));
    }

    public static function winningPaylinesDataProvider(): array
    {
        return [
            [
                'paylinesArray' => [
                    [[0, 0], [1, 0], [2, 0]],
                ],
                'winningPaylinesCount' => 1,
            ],
            [
                'paylinesArray' => [
                    [[0, 1], [1, 1], [2, 1]],
                ],
                'winningPaylinesCount' => 1,
            ],
            [
                'paylinesArray' => [
                    [[0, 2], [1, 2], [2, 2]],
                ],
                'winningPaylinesCount' => 1,
            ],
            [
                'paylinesArray' => [
                    [[0, 0], [1, 0], [2, 0]],
                    [[0, 1], [1, 1], [2, 1]],
                ],
                'winningPaylinesCount' => 2,
            ],
            [
                'paylinesArray' => [
                    [[0, 0], [1, 0], [2, 0]],
                    [[0, 1], [1, 1], [2, 1]],
                    [[0, 2], [1, 2], [2, 2]],
                ],
                'winningPaylinesCount' => 3,
            ],
        ];
    }
}
