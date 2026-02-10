<?php

namespace Tests\Feature\Services\Game;

use App\Models\Game;
use App\Services\Game\Contracts\RNGService;

readonly class DumbSlotRNGService implements RNGService
{
    public function __construct(private array $grid)
    {
    }

    public function generate(Game $game): array
    {
        return [
            'grid' => $this->grid,
        ];
    }
}
