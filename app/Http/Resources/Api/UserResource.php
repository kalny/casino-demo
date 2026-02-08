<?php

namespace App\Http\Resources\Api;

use App\DTO\Api\Auth\UserDTO;
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
        /** @var UserDTO $this */
        return [
            'token' => $this->token,
            'user' => [
                'name' => $this->name,
                'email' => $this->email,
            ]
        ];
    }
}
