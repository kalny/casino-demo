<?php

namespace Tests\Feature\Services\Game;

use App\DTO\Api\Game\PlayGameDTO;
use App\Enums\GameType;
use App\Exceptions\NotFoundException;
use App\Models\Game;
use App\Models\User;
use App\Services\Game\Exceptions\InsufficientFundsException;
use App\Services\Game\GameEngineService;
use App\Services\Game\PlayGameService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class GameEngineServiceTest extends TestCase
{
    use RefreshDatabase;

    private Game $game;
    private User $user;
    private GameEngineService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->game = Game::factory()->create();
        $this->user = User::factory()->create([
            'balance' => 1000
        ]);
        $this->service = app(GameEngineService::class);
    }

    public function testPlayDiceOverLoss(): void
    {
        $game = Game::factory()->create([
            'type' =>GameType::Dice,
        ]);

        $playGameDTO = new PlayGameDTO(
            amount: 100,
            params: [
                'number' => 4,
                'bet_type' => 'over'
            ]
        );

        $testGameResolver = new TestGameResolver(3, []);
        $service = new GameEngineService(
            $testGameResolver,
            new PlayGameService()
        );

        $gameResultDTO = $service->play($game->id, $this->user->id, $playGameDTO);

        $this->assertEquals(0, $gameResultDTO->payout);
        $this->assertEquals(900, $gameResultDTO->balance);
        $this->assertFalse($gameResultDTO->playResult['win']);
        $this->assertEquals(0, $gameResultDTO->playResult['multiplier']);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'balance' => 900,
        ]);

        $this->assertDatabaseHas('bets', [
            'user_id' => $this->user->id,
            'game_id' => $game->id,
            'amount' => 100,
            'result' => 'loss',
            'payout' => 0,
        ]);
    }

    public function testPlayDiceOverWin(): void
    {
        $game = Game::factory()->create([
            'type' => GameType::Dice,
        ]);

        $playGameDTO = new PlayGameDTO(
            amount: 100,
            params: [
                'number' => 4,
                'bet_type' => 'over'
            ]
        );

        $testGameResolver = new TestGameResolver(5, []);
        $service = new GameEngineService(
            $testGameResolver,
            new PlayGameService()
        );

        $gameResultDTO = $service->play($game->id, $this->user->id, $playGameDTO);

        $this->assertEquals(200, $gameResultDTO->payout);
        $this->assertEquals(1100, $gameResultDTO->balance);
        $this->assertTrue($gameResultDTO->playResult['win']);
        $this->assertEquals(2, $gameResultDTO->playResult['multiplier']);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'balance' => 1100,
        ]);

        $this->assertDatabaseHas('bets', [
            'user_id' => $this->user->id,
            'game_id' => $game->id,
            'amount' => 100,
            'result' => 'win',
            'payout' => 200,
        ]);
    }

    public function testPlayDiceUnderLoss(): void
    {
        $game = Game::factory()->create([
            'type' =>GameType::Dice,
        ]);

        $playGameDTO = new PlayGameDTO(
            amount: 100,
            params: [
                'number' => 4,
                'bet_type' => 'under'
            ]
        );

        $testGameResolver = new TestGameResolver(6, []);
        $service = new GameEngineService(
            $testGameResolver,
            new PlayGameService()
        );

        $gameResultDTO = $service->play($game->id, $this->user->id, $playGameDTO);

        $this->assertEquals(0, $gameResultDTO->payout);
        $this->assertEquals(900, $gameResultDTO->balance);
        $this->assertFalse($gameResultDTO->playResult['win']);
        $this->assertEquals(0, $gameResultDTO->playResult['multiplier']);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'balance' => 900,
        ]);

        $this->assertDatabaseHas('bets', [
            'user_id' => $this->user->id,
            'game_id' => $game->id,
            'amount' => 100,
            'result' => 'loss',
            'payout' => 0,
        ]);
    }

    public function testPlayDiceUnderWin(): void
    {
        $game = Game::factory()->create([
            'type' =>GameType::Dice,
        ]);

        $playGameDTO = new PlayGameDTO(
            amount: 100,
            params: [
                'number' => 4,
                'bet_type' => 'under'
            ]
        );

        $testGameResolver = new TestGameResolver(3, []);
        $service = new GameEngineService(
            $testGameResolver,
            new PlayGameService()
        );

        $gameResultDTO = $service->play($game->id, $this->user->id, $playGameDTO);

        $this->assertEquals(200, $gameResultDTO->payout);
        $this->assertEquals(1100, $gameResultDTO->balance);
        $this->assertTrue($gameResultDTO->playResult['win']);
        $this->assertEquals(2, $gameResultDTO->playResult['multiplier']);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'balance' => 1100,
        ]);

        $this->assertDatabaseHas('bets', [
            'user_id' => $this->user->id,
            'game_id' => $game->id,
            'amount' => 100,
            'result' => 'win',
            'payout' => 200,
        ]);
    }

    public function testPlaySlotLoss(): void
    {
        $game = Game::factory()->create([
            'type' =>GameType::Slot,
            'config' => [
                'symbols' => [
                    'A' => 5,
                    'B' => 6,
                    'C' => 7
                ],
                'reel_strip' => ['A', 'A', 'C', 'A', 'B', 'B'],
                'reels_number' => 3,
                'symbols_number' => 3,
                'paylines' => [
                    [[0, 1], [1, 1], [2, 1]] // middle line
                ]
            ]
        ]);

        $playGameDTO = new PlayGameDTO(
            amount: 100
        );

        $testGameResolver = new TestGameResolver(1, [
            ['A', 'A', 'C'],
            ['A', 'C', 'A'],
            ['E', 'A', 'A'],
        ]);
        $service = new GameEngineService(
            $testGameResolver,
            new PlayGameService()
        );

        $gameResultDTO = $service->play($game->id, $this->user->id, $playGameDTO);

        $this->assertEquals(0, $gameResultDTO->payout);
        $this->assertEquals(900, $gameResultDTO->balance);
        $this->assertFalse($gameResultDTO->playResult['win']);
        $this->assertEquals(0, $gameResultDTO->playResult['multiplier']);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'balance' => 900,
        ]);

        $this->assertDatabaseHas('bets', [
            'user_id' => $this->user->id,
            'game_id' => $game->id,
            'amount' => 100,
            'result' => 'loss',
            'payout' => 0,
        ]);
    }

    public function testPlaySlotWin(): void
    {
        $game = Game::factory()->create([
            'type' =>GameType::Slot,
            'config' => [
                'symbols' => [
                    'A' => 5,
                    'B' => 6,
                    'C' => 7
                ],
                'reel_strip' => ['A', 'A', 'C', 'A', 'B', 'B'],
                'reels_number' => 3,
                'symbols_number' => 3,
                'paylines' => [
                    [[0, 1], [1, 1], [2, 1]] // middle line
                ]
            ]
        ]);

        $playGameDTO = new PlayGameDTO(
            amount: 100
        );

        $testGameResolver = new TestGameResolver(1, [
            ['A', 'A', 'C'],
            ['C', 'A', 'E'],
            ['E', 'A', 'A'],
        ]);
        $service = new GameEngineService(
            $testGameResolver,
            new PlayGameService()
        );

        $gameResultDTO = $service->play($game->id, $this->user->id, $playGameDTO);

        $this->assertEquals(500, $gameResultDTO->payout);
        $this->assertEquals(1400, $gameResultDTO->balance);
        $this->assertTrue($gameResultDTO->playResult['win']);
        $this->assertEquals(5, $gameResultDTO->playResult['multiplier']);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'balance' => 1400,
        ]);

        $this->assertDatabaseHas('bets', [
            'user_id' => $this->user->id,
            'game_id' => $game->id,
            'amount' => 100,
            'result' => 'win',
            'payout' => 500,
        ]);
    }

    #[dataProvider('paylinesDataProvider')]
    public function testPlaySlotPaylines(array $grid, array $paylines, bool $win): void
    {
        $game = Game::factory()->create([
            'type' =>GameType::Slot,
            'config' => [
                'symbols' => [
                    'A' => 5,
                    'B' => 6,
                    'C' => 7
                ],
                'reel_strip' => ['A', 'A', 'C', 'A', 'B', 'B'],
                'reels_number' => 3,
                'symbols_number' => 3,
                'paylines' => $paylines
            ]
        ]);

        $playGameDTO = new PlayGameDTO(
            amount: 100
        );

        $testGameResolver = new TestGameResolver(1, $grid);
        $service = new GameEngineService(
            $testGameResolver,
            new PlayGameService()
        );

        $gameResultDTO = $service->play($game->id, $this->user->id, $playGameDTO);

        $this->assertEquals($win, $gameResultDTO->playResult['win']);
    }

    #[dataProvider('multiplierDataProvider')]
    public function testPlaySlotMultiplier(array $grid, array $paylines, int $multiplier): void
    {
        $game = Game::factory()->create([
            'type' =>GameType::Slot,
            'config' => [
                'symbols' => [
                    'A' => 5,
                    'B' => 6,
                    'C' => 7
                ],
                'reel_strip' => ['A', 'A', 'C', 'A', 'B', 'B'],
                'reels_number' => 3,
                'symbols_number' => 3,
                'paylines' => $paylines
            ]
        ]);

        $playGameDTO = new PlayGameDTO(
            amount: 100
        );

        $testGameResolver = new TestGameResolver(1, $grid);
        $service = new GameEngineService(
            $testGameResolver,
            new PlayGameService()
        );

        $gameResultDTO = $service->play($game->id, $this->user->id, $playGameDTO);

        $this->assertEquals($multiplier, $gameResultDTO->playResult['multiplier']);
    }

    public function testPlayGameNotFound(): void
    {
        $this->expectException(NotFoundException::class);

        $playGameDTO = new PlayGameDTO(
            amount: 100,
            params: []
        );

        $this->service->play(10, $this->user->id, $playGameDTO);
    }

    public function testPlayUserNotFound(): void
    {
        $this->expectException(NotFoundException::class);

        $playGameDTO = new PlayGameDTO(
            amount: 100,
            params: []
        );

        $this->service->play($this->game->id, 10, $playGameDTO);
    }

    public function testPlayInsufficientBalance(): void
    {
        $this->expectException(InsufficientFundsException::class);

        $userWithZeroBalance = User::factory()->create();

        $playGameDTO = new PlayGameDTO(
            amount: 100,
            params: []
        );

        $this->service->play($this->game->id, $userWithZeroBalance->id, $playGameDTO);
    }

    public static function paylinesDataProvider(): array
    {
        return [
            [
                'grid' => [
                    ['A', 'A', 'C'],
                    ['C', 'A', 'E'],
                    ['E', 'A', 'A'],
                ],
                'paylines' => [
                    [[0, 0], [1, 0], [2, 0]]
                ],
                'win' => false
            ],
            [
                'grid' => [
                    ['A', 'A', 'C'],
                    ['C', 'A', 'E'],
                    ['E', 'A', 'A'],
                ],
                'paylines' => [
                    [[0, 1], [1, 1], [2, 1]]
                ],
                'win' => true
            ],
            [
                'grid' => [
                    ['A', 'A', 'C'],
                    ['C', 'A', 'E'],
                    ['E', 'A', 'A'],
                ],
                'paylines' => [
                    [[0, 0], [1, 1], [2, 2]]
                ],
                'win' => true
            ],
            [
                'grid' => [
                    ['A', 'A', 'C'],
                    ['C', 'A', 'E'],
                    ['E', 'A', 'A'],
                ],
                'paylines' => [
                    [[0, 0], [1, 1], [2, 1]]
                ],
                'win' => true
            ],
            [
                'grid' => [
                    ['A', 'A', 'C'],
                    ['C', 'A', 'E'],
                    ['E', 'A', 'A'],
                ],
                'paylines' => [
                    [[0, 0], [1, 1], [2, 0]]
                ],
                'win' => false
            ],
            [
                'grid' => [
                    ['A', 'A', 'C'],
                    ['C', 'A', 'E'],
                    ['E', 'A', 'A'],
                ],
                'paylines' => [
                    [[0, 1], [1, 1], [2, 2]]
                ],
                'win' => true
            ],
            [
                'grid' => [
                    ['A', 'A', 'C'],
                    ['C', 'A', 'E'],
                    ['E', 'A', 'A'],
                ],
                'paylines' => [
                    [[0, 0], [1, 0], [2, 0]], // loss
                    [[0, 1], [1, 1], [2, 1]]  // win
                ],
                'win' => true
            ],
            [
                'grid' => [
                    ['A', 'A', 'C'],
                    ['C', 'A', 'E'],
                    ['E', 'A', 'A'],
                ],
                'paylines' => [
                    [[0, 1], [1, 1], [2, 1]], // win
                    [[0, 2], [1, 2], [2, 2]]  // loss
                ],
                'win' => true
            ],
            [
                'grid' => [
                    ['A', 'A', 'C'],
                    ['C', 'A', 'E'],
                    ['E', 'A', 'A'],
                ],
                'paylines' => [
                    [[0, 1], [1, 1], [2, 1]], // win
                    [[0, 1], [1, 1], [2, 2]]  // win
                ],
                'win' => true
            ],
        ];
    }

    public static function multiplierDataProvider(): array
    {
        return [
            [
                'grid' => [
                    ['A', 'A', 'C'],
                    ['C', 'A', 'E'],
                    ['E', 'A', 'A'],
                ],
                'paylines' => [
                    [[0, 1], [1, 1], [2, 1]]
                ],
                'multiplier' => 5
            ],
            [
                'grid' => [
                    ['A', 'A', 'C'],
                    ['C', 'A', 'E'],
                    ['E', 'A', 'A'],
                ],
                'paylines' => [
                    [[0, 1], [1, 1], [2, 1]],
                    [[0, 1], [1, 1], [2, 2]]
                ],
                'multiplier' => 10
            ],
            [
                'grid' => [
                    ['A', 'A', 'C'],
                    ['A', 'C', 'A'],
                    ['C', 'A', 'B'],
                ],
                'paylines' => [
                    [[0, 0], [1, 0], [2, 1]],
                    [[0, 2], [1, 1], [2, 0]]
                ],
                'multiplier' => 12
            ],
            [
                'grid' => [
                    ['A', 'A', 'C'],
                    ['A', 'C', 'A'],
                    ['C', 'A', 'B'],
                ],
                'paylines' => [
                    [[0, 0], [1, 0], [2, 1]],
                    [[0, 2], [1, 1], [2, 0]],
                    [[0, 1], [1, 2]],
                ],
                'multiplier' => 17
            ],
            [
                'grid' => [
                    ['A', 'A', 'C'],
                    ['A', 'C', 'A'],
                    ['C', 'A', 'B'],
                ],
                'paylines' => [
                    [[2, 2]],
                ],
                'multiplier' => 6
            ],
            [
                'grid' => [
                    ['A', 'A', 'C'],
                    ['A', 'C', 'A'],
                    ['C', 'A', 'B'],
                ],
                'paylines' => [
                    [[0, 0], [1, 0], [2, 1]],
                    [[0, 2], [1, 1], [2, 0]],
                    [[0, 1], [1, 2]],
                    [[2, 2]],
                ],
                'multiplier' => 23
            ],
        ];
    }
}
