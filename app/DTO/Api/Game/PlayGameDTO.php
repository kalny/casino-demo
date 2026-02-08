<?php

namespace App\DTO\Api\Game;

readonly class PlayGameDTO
{
    public function __construct(
        public int $amount,
        public ?array $params = null
    ) {
    }
}
