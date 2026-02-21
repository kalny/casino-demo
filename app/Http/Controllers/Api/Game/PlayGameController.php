<?php

namespace App\Http\Controllers\Api\Game;

use App\Application\GameResolver;
use App\Domain\Exceptions\InsufficientFundsException;
use App\Domain\Exceptions\InvalidArgumentException;
use App\Domain\Exceptions\InvalidGameTypeException;
use App\Domain\Games\GameId;
use App\Domain\Games\Repository\GameRepository;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Game\PlayGameRequest;
use App\Http\Resources\Api\Game\GameResultResource;

class PlayGameController extends Controller
{
    /**
     * @throws InvalidArgumentException
     * @throws InsufficientFundsException
     * @throws InvalidGameTypeException
     */
    public function play(
        string $id,
        PlayGameRequest $request,
        GameResolver $gameResolver,
        GameRepository $gameRepository,
    ): GameResultResource {
        $gameType = $gameRepository->getTypeById(new GameId($id));
        $gameOutcome = $gameResolver->resolveGame($gameType, $request->validated(), $id, $request->user()->id);

        return new GameResultResource($gameOutcome);
    }
}
