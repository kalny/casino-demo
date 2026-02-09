<?php

namespace App\Http\Resources\Api\Game;

use App\DTO\Api\Game\GameResultDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResultResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var GameResultDTO $this */

        return [
            'result' => $this->result->value,
            'payout' => $this->payout,
            'balance' => $this->balance,
            'play_result' => $this->playResult,
        ];
    }
}
