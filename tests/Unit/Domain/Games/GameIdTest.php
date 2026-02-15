<?php

namespace Tests\Unit\Domain\Games;

use App\Domain\Exceptions\InvalidArgumentException;
use App\Domain\Games\GameId;
use Tests\TestCase;

class GameIdTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     */
    public function testCreateValidGameIdFromInteger(): void
    {
        $gameId = new GameId(1);

        $this->assertSame(1, $gameId->getValue());
    }

    public function testCreateValidGameIdWithDefaultZeroValue(): void
    {
        $gameId = new GameId();

        $this->assertSame(0, $gameId->getValue());
    }

    public function testCreateValidGameIdWithNegativeValue(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new GameId(-1);
    }
}
