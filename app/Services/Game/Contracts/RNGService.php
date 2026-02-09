<?php

namespace App\Services\Game\Contracts;

use App\Models\Game;

interface RNGService
{
    public function generate(Game $game): array;
}
