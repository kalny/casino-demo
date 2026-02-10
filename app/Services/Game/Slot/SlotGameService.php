<?php

namespace App\Services\Game\Slot;

use App\Models\Game;
use App\Services\Game\Contracts\GameService;
use App\Services\Game\Contracts\RNGService;
use App\Services\Game\Exceptions\InvalidConfigException;

class SlotGameService implements GameService
{
    /**
     * @throws InvalidConfigException
     */
    public function play(Game $game, RNGService $rng, ?array $params): array
    {
        $paylines = $game->config['paylines'] ?? null;
        $symbols = $game->config['symbols'] ?? null;

        if (!$paylines || !$symbols) {
            throw new InvalidConfigException('Invalid game config');
        }

        $random = $rng->generate($game);
        $grid = $random['grid'];

        $winningPaylines = $this->getWinningPaylines($grid, $paylines);
        $win = !empty($winningPaylines['paylines']);

        return [
            'win' => $win,
            'multiplier' => $win
                ? $this->getMultiplier(
                    $symbols,
                    $winningPaylines['symbols']
                )
                : 0,
            'grid' => $grid,
            'winning_paylines' => $winningPaylines['paylines']
        ];
    }

    private function getWinningPaylines(array $grid, array $paylines): array
    {
        $winPaylines = [
            'paylines' => [],
            'symbols' => [],
        ];

        foreach ($paylines as $payline) {
            $paylineSymbols = [];

            foreach ($payline as $element) {
                [$reel, $symbol] = $element;
                $paylineSymbols[] = $grid[$reel][$symbol] ?? null;
            }

            $win = count(array_unique($paylineSymbols)) === 1;
            if ($win) {
                $winPaylines['paylines'][] = $payline;
                $winPaylines['symbols'][] = $paylineSymbols[0];
            }
        }

        return $winPaylines;
    }

    /**
     * @throws InvalidConfigException
     */
    private function getMultiplier(array $symbols, array $winningSymbols): int
    {
        $multiplier = 0;

        foreach ($winningSymbols as $winningSymbol) {
            if (!isset($symbols[$winningSymbol])) {
                throw new InvalidConfigException('Invalid symbol: ' . $winningSymbol);
            }
            $multiplier += $symbols[$winningSymbol];
        }

        return $multiplier;
    }
}
