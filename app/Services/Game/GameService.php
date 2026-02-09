<?php

namespace App\Services\Game;

use App\Models\Game;

interface GameService
{
    public function play(Game $game, RNGService $rng, ?array $params): array;
}
