<?php

namespace App\Http\Controllers\Api\Game;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Game\GameResource;
use App\Models\Game;
use Illuminate\Http\JsonResponse;

class GameController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Game::paginate());
    }

    public function show(int $id): GameResource
    {
        $game = Game::findOrFail($id);

        return new GameResource($game);
    }
}
