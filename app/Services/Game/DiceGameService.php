<?php

namespace App\Services\Game;

use App\Models\Game;

class DiceGameService implements GameService
{
    private const DEFAULT_BET = 3;
    private const DEFAULT_MULTIPLIER = 2;

    public function play(Game $game, RNGService $rng, ?array $params): array
    {
        $random = $rng->generate($game);

        $roll = $random['roll'];
        $bet = $params['number'] ?? self::DEFAULT_BET;
        $type = $params['bet_type'] ?? 'over';

        $win = $type === 'over'
            ? $roll > $bet
            : $roll < $bet;

        return [
            'win' => $win,
            'multiplier' => $win
                ? self::DEFAULT_MULTIPLIER
                : 0
        ];
    }
}
