<?php

namespace App\Http\Resources\Api\Game;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Game $this */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'config' => $this->config,
        ];
    }
}
