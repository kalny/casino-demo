<?php

namespace App\Services\Game;

use App\Models\Game;

class SlotGameService implements GameService
{
    private const DEFAULT_MULTIPLIER = 5;

    public function play(Game $game, RNGService $rng, ?array $params): array
    {
        $random = $rng->generate($game);

        $reels = $random['reels'];

        $win = count(array_unique($reels)) === 1;

        return [
            'win' => $win,
            'multiplier' => $win
                ? self::DEFAULT_MULTIPLIER
                : 0,
        ];
    }
}
