<?php

namespace Tests\Feature\Services\Game;

use App\Models\Game;
use App\Services\Game\RNGService;

readonly class DumbSlotRNGService implements RNGService
{
    public function __construct(private array $reels)
    {
    }

    public function generate(Game $game): array
    {
        return [
            'reels' => $this->reels,
        ];
    }
}
