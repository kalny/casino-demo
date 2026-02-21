<?php

namespace Tests\Unit\Domain\Game;

use App\Domain\Game\GameId;
use Tests\TestCase;

class GameIdTest extends TestCase
{
    public function testCreateValidGameIdFromInteger(): void
    {
        $gameId = GameId::fromString('id');

        $this->assertSame('id', $gameId->getValue());
    }
}
