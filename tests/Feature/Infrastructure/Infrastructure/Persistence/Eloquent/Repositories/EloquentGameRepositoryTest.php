<?php

namespace Tests\Feature\Infrastructure\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Exceptions\InvalidArgumentException;
use App\Domain\Exceptions\InvalidGameConfigException;
use App\Domain\Games\Common\GameType;
use App\Domain\Games\GameId;
use App\Infrastructure\Persistence\Eloquent\Models\Game;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentGameRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentGameRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EloquentGameRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new EloquentGameRepository();
    }

    public function testGetTypeById(): void
    {
        $gameEloquentModel = Game::factory()->create([
            'type' => GameType::Slot->value
        ]);

        $type = $this->repository->getTypeById(new GameId($gameEloquentModel->id));

        $this->assertSame(GameType::Slot->value, $type->value);
    }

    public function testGetTypeByIdFailed(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->repository->getTypeById(new GameId('id'));
    }

    /**
     * @throws InvalidGameConfigException
     * @throws InvalidArgumentException
     */
    public function testGetDiceGameById(): void
    {
        $diceGame = Game::factory()->create([
            'type' => GameType::Dice->value,
            'config' => [
                'multiplier' => 2
            ]
        ]);

        $game = $this->repository->getDiceGameById(new GameId($diceGame->id));

        $this->assertSame($diceGame->id, $game->getId()->getValue());
    }

    /**
     * @throws InvalidGameConfigException
     * @throws InvalidArgumentException
     */
    public function testGetDiceGameByIdFailedId(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->repository->getDiceGameById(new GameId('id'));
    }

    /**
     * @throws InvalidGameConfigException
     * @throws InvalidArgumentException
     */
    public function testGetDiceGameByIdFailedType(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $slotGame = Game::factory()->create([
            'type' => GameType::Slot->value,
            'config' => [
                'multiplier' => 2
            ]
        ]);

        $this->repository->getDiceGameById(new GameId($slotGame->id));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testGetDiceGameByIdInvalidConfigMultiplier(): void
    {
        $this->expectException(InvalidGameConfigException::class);

        $diceGame = Game::factory()->create([
            'type' => GameType::Dice->value,
            'config' => []
        ]);

        $this->repository->getDiceGameById(new GameId($diceGame->id));
    }

    /**
     * @throws InvalidGameConfigException
     * @throws InvalidArgumentException
     */
    public function testGetSlotGameById(): void
    {
        $slotGame = Game::factory()->create([
            'type' => GameType::Slot->value,
            'config' => [
                'reels_number' => 3,
                'symbols_number' => 3,
                'reel_strip' => ['A', 'B', 'C'],
                'symbols' => [
                    ['name' => 'A', 'multiplier' => 5],
                    ['name' => 'B', 'multiplier' => 6],
                    ['name' => 'C', 'multiplier' => 7]
                ],
                'paylines' => [[[0, 1], [0, 2], [0, 3]]]
            ]
        ]);

        $game = $this->repository->getSlotGameById(new GameId($slotGame->id));

        $this->assertSame($slotGame->id, $game->getId()->getValue());
    }

    /**
     * @throws InvalidGameConfigException
     * @throws InvalidArgumentException
     */
    public function testGetSlotGameByIdFailedId(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->repository->getSlotGameById(new GameId('id'));
    }

    /**
     * @throws InvalidGameConfigException
     * @throws InvalidArgumentException
     */
    public function testGetSlotGameByIdFailedType(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $diceGame = Game::factory()->create([
            'type' => GameType::Dice->value,
            'config' => [
                'multiplier' => 2
            ]
        ]);

        $this->repository->getSlotGameById(new GameId($diceGame->id));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testGetSlotGameByIdInvalidConfigReelsNumber(): void
    {
        $this->expectException(InvalidGameConfigException::class);

        $slotGame = Game::factory()->create([
            'type' => GameType::Slot->value,
            'config' => [
                'symbols_number' => 3,
                'reel_strip' => ['A', 'B', 'C'],
                'symbols' => [
                    ['name' => 'A', 'multiplier' => 5],
                    ['name' => 'B', 'multiplier' => 6],
                    ['name' => 'C', 'multiplier' => 7]
                ],
                'paylines' => [[[0, 1], [0, 2], [0, 3]]]
            ]
        ]);

        $this->repository->getSlotGameById(new GameId($slotGame->id));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testGetSlotGameByIdInvalidConfigSymbolsNumber(): void
    {
        $this->expectException(InvalidGameConfigException::class);

        $slotGame = Game::factory()->create([
            'type' => GameType::Slot->value,
            'config' => [
                'reels_number' => 3,
                'reel_strip' => ['A', 'B', 'C'],
                'symbols' => [
                    ['name' => 'A', 'multiplier' => 5],
                    ['name' => 'B', 'multiplier' => 6],
                    ['name' => 'C', 'multiplier' => 7]
                ],
                'paylines' => [[[0, 1], [0, 2], [0, 3]]]
            ]
        ]);

        $this->repository->getSlotGameById(new GameId($slotGame->id));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testGetSlotGameByIdInvalidConfigReelStrip(): void
    {
        $this->expectException(InvalidGameConfigException::class);

        $slotGame = Game::factory()->create([
            'type' => GameType::Slot->value,
            'config' => [
                'reels_number' => 3,
                'symbols_number' => 3,
                'symbols' => [
                    ['name' => 'A', 'multiplier' => 5],
                    ['name' => 'B', 'multiplier' => 6],
                    ['name' => 'C', 'multiplier' => 7]
                ],
                'paylines' => [[[0, 1], [0, 2], [0, 3]]]
            ]
        ]);

        $this->repository->getSlotGameById(new GameId($slotGame->id));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testGetSlotGameByIdInvalidConfigSymbols(): void
    {
        $this->expectException(InvalidGameConfigException::class);

        $slotGame = Game::factory()->create([
            'type' => GameType::Slot->value,
            'config' => [
                'reels_number' => 3,
                'symbols_number' => 3,
                'reel_strip' => ['A', 'B', 'C'],
                'paylines' => [[[0, 1], [0, 2], [0, 3]]]
            ]
        ]);

        $this->repository->getSlotGameById(new GameId($slotGame->id));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testGetSlotGameByIdInvalidConfigPaylines(): void
    {
        $this->expectException(InvalidGameConfigException::class);

        $slotGame = Game::factory()->create([
            'type' => GameType::Slot->value,
            'config' => [
                'reels_number' => 3,
                'symbols_number' => 3,
                'reel_strip' => ['A', 'B', 'C'],
                'symbols' => [
                    ['name' => 'A', 'multiplier' => 5],
                    ['name' => 'B', 'multiplier' => 6],
                    ['name' => 'C', 'multiplier' => 7]
                ],
            ]
        ]);

        $this->repository->getSlotGameById(new GameId($slotGame->id));
    }
}
