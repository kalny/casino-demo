<?php

namespace Tests\Feature\Http\Controllers\Api\Game;

use App\Domain\Games\Common\GameType;
use App\Infrastructure\Persistence\Eloquent\Models\Game;
use App\Infrastructure\Persistence\Eloquent\Models\User;
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

    public function testPlaySlot(): void
    {
        Sanctum::actingAs($this->user);

        $game = Game::factory()->create([
            'name' => 'Test Game',
            'type' => GameType::Slot,
            'config' => [
                'symbols' => [
                    ['name' => 'A', 'multiplier' => 5],
                    ['name' => 'B', 'multiplier' => 6],
                    ['name' => 'C', 'multiplier' => 7],
                ],
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
                'play_result' => [
                    'multiplier',
                    'grid',
                    'winning_paylines'
                ]
            ]
        ]);
    }

    public function testPlaySlotWithInvalidConfig(): void
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

        $response->assertStatus(400);
    }

    public function testPlayDice(): void
    {
        Sanctum::actingAs($this->user);

        $game = Game::factory()->create([
            'name' => 'Test Game',
            'type' => GameType::Dice,
            'config' => [
                'multiplier' => 2
            ]
        ]);

        $payload = [
            'amount' => 100,
            'params' => [
                'number' => 1,
                'bet_type' => 'over',
            ]
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
                'play_result' => [
                    'multiplier',
                    'roll',
                ]
            ]
        ]);
    }

    public function testPlayDiceInvalidParams(): void
    {
        Sanctum::actingAs($this->user);

        $game = Game::factory()->create([
            'name' => 'Test Game',
            'type' => GameType::Dice,
            'config' => [
                'multiplier' => 2
            ]
        ]);

        $payload = [
            'amount' => 100,
        ];

        $response = $this->postJson(
            route('api.games.play', ['id' => $game->id]),
            $payload
        );

        $response->assertStatus(422);
    }

    public function testPlayDiceInvalidGameConfig(): void
    {
        Sanctum::actingAs($this->user);

        $game = Game::factory()->create([
            'name' => 'Test Game',
            'type' => GameType::Dice,
            'config' => []
        ]);

        $payload = [
            'amount' => 100,
            'params' => [
                'number' => 1,
                'bet_type' => 'over',
            ]
        ];

        $response = $this->postJson(
            route('api.games.play', ['id' => $game->id]),
            $payload
        );

        $response->assertStatus(400);
    }

    public function testPlayUnauthorized(): void
    {
        $game = Game::factory()->create([
            'name' => 'Test Game',
            'type' => GameType::Slot,
            'config' => [
                'symbols' => [
                    'A' => 5,
                    'B' => 6,
                    'C' => 7
                ],
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
                'symbols' => [
                    'A' => 5,
                    'B' => 6,
                    'C' => 7
                ],
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
