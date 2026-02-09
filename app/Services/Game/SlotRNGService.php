<?php

namespace App\Services\Game;

use App\Models\Game;
use App\Services\Game\Exceptions\InvalidConfigException;

class SlotRNGService implements RNGService
{
    /**
     * @throws InvalidConfigException
     */
    public function generate(Game $game): array
    {
        $symbols = $game->config['symbols'] ?? null;

        if (!$symbols) {
            throw new InvalidConfigException('Invalid game config');
        }

        $reels = [
            $symbols[array_rand($symbols)],
            $symbols[array_rand($symbols)],
            $symbols[array_rand($symbols)],
        ];

        return [
            'reels' => $reels
        ];
    }
}
