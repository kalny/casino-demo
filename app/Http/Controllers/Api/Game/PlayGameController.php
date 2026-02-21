<?php

namespace App\Http\Controllers\Api\Game;

use App\Application\GameResolver;
use App\Domain\Common\Exceptions\InsufficientFundsException;
use App\Domain\Common\Exceptions\InvalidArgumentException;
use App\Domain\Game\Exceptions\InvalidGameTypeException;
use App\Domain\Game\GameId;
use App\Domain\Game\Repository\GameRepository;
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
        $gameType = $gameRepository->getTypeById(GameId::fromString($id));
        $gameOutcome = $gameResolver->resolveGame($gameType, $request->validated(), $id, $request->user()->id);

        return new GameResultResource($gameOutcome);
    }
}
