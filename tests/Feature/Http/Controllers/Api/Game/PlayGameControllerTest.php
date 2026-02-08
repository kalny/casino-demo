<?php

namespace Tests\Feature\Http\Controllers\Api\Game;

use App\Enums\GameType;
use App\Models\Game;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PlayGameControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testPlay(): void
    {
        $game = Game::factory()->create([
            'name' => 'Test Game',
            'type' => GameType::Slot,
            'config' => ['test' => true]
        ]);

        $payload = [
            'amount' => 100,
        ];

        $response = $this->postJson(
            route('api.games.play', ['id' => $game->id]),
            $payload
        );

        $response->assertStatus(200);
        $response->assertJsonPath('data', [
            'game_id' => $game->id,
            'amount' => 100
        ]);
    }

    #[dataProvider('validationDataProvider')]
    public function testPlayValidation(array $payload, array $errors): void
    {
        $game = Game::factory()->create([
            'name' => 'Test Game',
            'type' => GameType::Slot,
            'config' => ['test' => true]
        ]);

        $response = $this->postJson(
            route('api.games.play', ['id' => $game->id]),
            $payload
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($errors);
    }

    public static function validationDataProvider(): array
    {
        return [
            [
                'payload' => [],
                'errors' => [
                    'amount' => ['The amount field is required.'],
                ]
            ],
            [
                'payload' => [
                    'amount' => 'wrong'
                ],
                'errors' => [
                    'amount' => ['The amount field must be an integer.'],
                ]
            ],
            [
                'payload' => [
                    'amount' => 0
                ],
                'errors' => [
                    'amount' => ['The amount field must be at least 1.'],
                ]
            ],
            [
                'payload' => [
                    'params' => 0
                ],
                'errors' => [
                    'params' => ['The params field must be an array.'],
                ]
            ]
        ];
    }
}
