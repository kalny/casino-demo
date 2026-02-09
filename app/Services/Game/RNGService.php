<?php

namespace App\Services\Game;

use App\Models\Game;

interface RNGService
{
    public function generate(Game $game): array;
}
