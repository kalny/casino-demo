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
            'game_id' => $this->gameId,
            'user_id' => $this->userId,
            'amount' => $this->amount,
        ];
    }
}
