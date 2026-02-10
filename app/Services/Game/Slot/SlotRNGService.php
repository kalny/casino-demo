<?php

namespace App\Services\Game\Slot;

use App\Models\Game;
use App\Services\Game\Contracts\RNGService;
use App\Services\Game\Exceptions\InvalidConfigException;
use Throwable;

class SlotRNGService implements RNGService
{
    /**
     * @throws InvalidConfigException
     */
    public function generate(Game $game): array
    {
        $reelStrip = $game->config['reel_strip'] ?? null;
        $reelsNumber = $game->config['reels_number'] ?? null;
        $symbolsNumber = $game->config['symbols_number'] ?? null;

        if (!$reelStrip || !$reelsNumber || !$symbolsNumber) {
            throw new InvalidConfigException('Invalid game config');
        }

        $grid = [];

        for ($i = 0; $i < $reelsNumber; $i++) {
            $pos = 0;
            try {
                $pos = random_int(0, count($reelStrip) - 1);
            } catch (Throwable) {}

            $reel = [];

            for ($j = 0; $j < $symbolsNumber; $j++) {
                $reel[] = $reelStrip[($pos + $j) % count($reelStrip)];
            }

            $grid[] = $reel;
        }

        return [
            'grid' => $grid
        ];
    }
}
