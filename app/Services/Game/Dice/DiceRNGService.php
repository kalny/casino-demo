<?php

namespace App\Services\Game\Dice;

use App\Models\Game;
use App\Services\Game\Contracts\RNGService;
use Throwable;

class DiceRNGService implements RNGService
{
    private const MIN_ROLL = 1;
    private const MAX_ROLL = 6;

    public function generate(Game $game): array
    {
        $roll = self::MIN_ROLL;

        try {
            $roll = random_int(self::MIN_ROLL, self::MAX_ROLL);
        } catch (Throwable) {}

        return [
            'roll' => $roll,
        ];
    }
}
