<?php

namespace App\Services\Game;

use App\DTO\Api\Game\GameResultDTO;
use App\DTO\Api\Game\PlayGameDTO;
use App\Enums\BetResult;
use App\Exceptions\NotFoundException;
use App\Models\Bet;
use App\Models\Game;
use App\Models\User;
use App\Services\Game\Contracts\GameFactory;
use App\Services\Game\Exceptions\InsufficientFundsException;
use Illuminate\Support\Facades\DB;
use Throwable;

class GameEngineService
{
    public function __construct(private readonly GameFactory $gameResolver)
    {
    }

    /**
     * @throws NotFoundException
     * @throws InsufficientFundsException
     * @throws Throwable
     */
    public function play(int $gameId, int $userId, PlayGameDTO $playGameDTO): GameResultDTO
    {
        $game = $this->getGame($gameId);
        $user = $this->getUser($userId);

        if ($user->balance < $playGameDTO->amount) {
            throw new InsufficientFundsException('Insufficient balance');
        }

        $user->balance -= $playGameDTO->amount;

        $rngService = $this->gameResolver->getRngService($game->type);
        $gameService = $this->gameResolver->getGameService($game->type);

        $playResult = $gameService->play($game, $rngService, $playGameDTO->params);

        $payout = $playResult['win']
            ? $playGameDTO->amount * $playResult['multiplier']
            : 0;

        if ($payout > 0) {
            $user->balance += $payout;
        }

        return DB::transaction(function () use ($game, $user, $playGameDTO, $playResult, $payout) {
            $user->save();

            /** @var Bet $bet */
            $bet = Bet::create([
                'user_id' => $user->id,
                'game_id' => $game->id,
                'amount' => $playGameDTO->amount,
                'bet_data' => $playResult,
                'result' => $playResult['win'] ? BetResult::Win : BetResult::Loss,
                'payout' => $payout,
            ]);

            return new GameResultDTO(
                result: $bet->result,
                payout: $bet->payout,
                balance: $user->balance,
                playResult: $playResult
            );
        });
    }

    /**
     * @throws NotFoundException
     */
    private function getGame(int $gameId): Game
    {
        $game = Game::find($gameId);
        if (!$game) {
            throw new NotFoundException('Game not found');
        }
        return $game;
    }

    /**
     * @throws NotFoundException
     */
    private function getUser(int $userId): User
    {
        $user = User::find($userId);
        if (!$user) {
            throw new NotFoundException('User not found');
        }
        return $user;
    }
}
