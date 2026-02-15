<?php

namespace App\Http\Resources\Api;

use App\Application\UseCase\UserResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var UserResponse $this */
        return [
            'token' => $this->token,
            'user' => [
                'id' => $this->id
            ]
        ];
    }
}
