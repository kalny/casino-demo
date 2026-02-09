<?php

namespace Tests\Feature\Services\Game;

use App\Models\Game;
use App\Services\Game\RNGService;

readonly class DumbDiceRNGService implements RNGService
{
    public function __construct(private int $roll)
    {
    }

    public function generate(Game $game): array
    {
        return [
            'roll' => $this->roll,
        ];
    }
}
