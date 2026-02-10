<?php

namespace Tests\Feature\Http\Controllers\Api\Game;

use App\Enums\GameType;
use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PlayGameControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'balance' => 1000
        ]);
    }

    public function testPlay(): void
    {
        Sanctum::actingAs($this->user);

        $game = Game::factory()->create([
            'name' => 'Test Game',
            'type' => GameType::Slot,
            'config' => [
                'symbols' => ['A', 'B', 'C'],
                'reel_strip' => ['A', 'A', 'C', 'A', 'B', 'B'],
                'reels_number' => 3,
                'symbols_number' => 3,
                'paylines' => [
                    [[0, 1], [1, 1], [2, 1]] // middle line
                ]
            ]
        ]);

        $payload = [
            'amount' => 100,
        ];

        $response = $this->postJson(
            route('api.games.play', ['id' => $game->id]),
            $payload
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'result',
                'payout',
                'balance',
                'play_result' => [
                    'win',
                    'multiplier'
                ]
            ]
        ]);
    }

    public function testPlayWithInvalidConfig(): void
    {
        Sanctum::actingAs($this->user);

        $game = Game::factory()->create([
            'name' => 'Test Game',
            'type' => GameType::Slot
        ]);

        $payload = [
            'amount' => 100,
        ];

        $response = $this->postJson(
            route('api.games.play', ['id' => $game->id]),
            $payload
        );

        $response->assertStatus(500);
    }

    public function testPlayUnauthorized(): void
    {
        $game = Game::factory()->create([
            'name' => 'Test Game',
            'type' => GameType::Slot,
            'config' => [
                'symbols' => ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'],
            ]
        ]);

        $payload = [
            'amount' => 100,
        ];

        $response = $this->postJson(
            route('api.games.play', ['id' => $game->id]),
            $payload
        );

        $response->assertStatus(401);
    }

    #[dataProvider('validationDataProvider')]
    public function testPlayValidation(array $payload, array $errors): void
    {
        Sanctum::actingAs($this->user);

        $game = Game::factory()->create([
            'name' => 'Test Game',
            'type' => GameType::Slot,
            'config' => [
                'symbols' => ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'],
            ]
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
