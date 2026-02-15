<?php

namespace App\Http\Resources\Api\Game;

use App\Domain\Games\Common\GameOutcome;
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
        /** @var GameOutcome $this */

        return [
            'result' => $this->outcomeStatus->value,
            'payout' => $this->winAmount->getValue(),
            'play_result' => $this->gameSpecificOutcome->jsonSerialize(),
        ];
    }
}
