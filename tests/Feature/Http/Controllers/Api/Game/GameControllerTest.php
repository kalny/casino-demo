<?php

namespace Tests\Feature\Http\Controllers\Api\Game;

use App\Enums\GameType;
use App\Models\Game;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex(): void
    {
        Game::factory(20)->create();

        $response = $this->getJson(route('api.games.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(15, 'data');
        $response->assertJsonPath('meta.total', 20);
    }

    public function testShow(): void
    {
        $game = Game::factory()->create([
            'name' => 'Test Game',
            'type' => GameType::Slot,
            'config' => ['test' => true]
        ]);

        $response = $this->getJson(route('api.games.show', ['id' => $game->id]));

        $response->assertStatus(200);
        $response->assertJsonPath('data', [
            'id' => $game->id,
            'name' => $game->name,
            'type' => $game->type->value,
            'config' => $game->config,
        ]);
    }

    public function testShowNotFound(): void
    {
        $response = $this->getJson(route('api.games.show', ['id' => 1]));

        $response->assertStatus(404);
    }
}
