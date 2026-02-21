<?php

namespace Tests\Feature\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Common\ValueObjects\BetAmount;
use App\Domain\Common\ValueObjects\BetMultiplier;
use App\Domain\Common\ValueObjects\WinAmount;
use App\Domain\Common\Exceptions\InvalidArgumentException;
use App\Domain\Game\GameOutcome;
use App\Domain\Game\OutcomeStatus;
use App\Domain\Game\Dice\DiceSpecificOutcome;
use App\Domain\Game\Dice\ValueObjects\DiceNumber;
use App\Domain\Game\GameId;
use App\Domain\User\UserId;
use App\Infrastructure\Persistence\Eloquent\Models\Game;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentGameOutcomeRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentGameOutcomeRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws InvalidArgumentException
     */
    public function testSave(): void
    {
        $gameEloquentModel = Game::factory()->create();
        $userEloquentModel = User::factory()->create();

        $eloquentGameOutcomeRepository = new EloquentGameOutcomeRepository();

        $eloquentGameOutcomeRepository->save(new GameOutcome(
            gameId: GameId::fromString($gameEloquentModel->id),
            userId: UserId::fromString($userEloquentModel->id),
            betAmount: BetAmount::fromInt(100),
            winAmount: WinAmount::fromInt(200),
            outcomeStatus: OutcomeStatus::Win,
            gameSpecificOutcome: new DiceSpecificOutcome(
                multiplier: BetMultiplier::fromInt(2),
                roll: DiceNumber::fromInt(5)
            )
        ));

        $this->assertDatabaseHas('bets', [
            'game_id' => $gameEloquentModel->id,
            'user_id' => $userEloquentModel->id,
            'amount' => 100,
            'result' => 'win',
            'payout' => 200
        ]);
    }
}
