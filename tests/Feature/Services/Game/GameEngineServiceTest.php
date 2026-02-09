<?php

namespace Tests\Feature\Services\Game;

use App\DTO\Api\Game\PlayGameDTO;
use App\Enums\GameType;
use App\Exceptions\NotFoundException;
use App\Models\Game;
use App\Models\User;
use App\Services\Game\Exceptions\InsufficientFundsException;
use App\Services\Game\GameEngineService;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $service = new GameEngineService($testGameResolver);

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
        $service = new GameEngineService($testGameResolver);

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
        $service = new GameEngineService($testGameResolver);

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
        $service = new GameEngineService($testGameResolver);

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
                'symbols' => ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'],
            ]
        ]);

        $playGameDTO = new PlayGameDTO(
            amount: 100
        );

        $testGameResolver = new TestGameResolver(1, ['A', 'B', 'C']);
        $service = new GameEngineService($testGameResolver);

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
                'symbols' => ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'],
            ]
        ]);

        $playGameDTO = new PlayGameDTO(
            amount: 100
        );

        $testGameResolver = new TestGameResolver(1, ['A', 'A', 'A']);
        $service = new GameEngineService($testGameResolver);

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
}
