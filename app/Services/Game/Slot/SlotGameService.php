<?php

namespace App\Services\Game\Slot;

use App\Models\Game;
use App\Services\Game\Contracts\GameService;
use App\Services\Game\Contracts\RNGService;
use App\Services\Game\Exceptions\InvalidConfigException;

class SlotGameService implements GameService
{
    private const DEFAULT_MULTIPLIER = 5;

    /**
     * @throws InvalidConfigException
     */
    public function play(Game $game, RNGService $rng, ?array $params): array
    {
        $random = $rng->generate($game);
        $grid = $random['grid'];

        $paylines = $game->config['paylines'] ?? null;
        if (!$paylines) {
            throw new InvalidConfigException('Invalid game config');
        }

        $win = $this->checkPaylines($grid, $paylines);

        return [
            'win' => $win,
            'multiplier' => $win
                ? self::DEFAULT_MULTIPLIER
                : 0,
            'grid' => $grid
        ];
    }

    private function checkPaylines(array $grid, array $paylines): bool
    {
        foreach ($paylines as $payline) {

            $paylineSymbols = [];

            foreach ($payline as $element) {
                [$reel, $symbol] = $element;
                $paylineSymbols[] = $grid[$reel][$symbol] ?? null;
            }

            $win = count(array_unique($paylineSymbols)) === 1;
            if ($win) {
                return true;
            }

        }
        return false;
    }
}
