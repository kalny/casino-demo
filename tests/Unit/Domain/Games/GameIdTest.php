<?php

namespace Tests\Unit\Domain\Games;

use App\Domain\Games\GameId;
use Tests\TestCase;

class GameIdTest extends TestCase
{
    public function testCreateValidGameIdFromInteger(): void
    {
        $gameId = new GameId('id');

        $this->assertSame('id', $gameId->getValue());
    }
}
