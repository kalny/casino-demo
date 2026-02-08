<?php

namespace App\Http\Controllers\Api\Game;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Game\PlayGameRequest;
use App\Http\Resources\Api\Game\GameResultResource;
use App\Services\Game\GameEngineService;

class PlayGameController extends Controller
{
    public function play(
        int $id,
        PlayGameRequest $request,
        GameEngineService $gameEngineService
    ): GameResultResource {
        return new GameResultResource(
            $gameEngineService->play(
                $id,
                $request->user()->id,
                $request->getDTO()
            )
        );
    }
}
