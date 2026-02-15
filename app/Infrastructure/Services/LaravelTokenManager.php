<?php

namespace App\Infrastructure\Services;

use App\Domain\Services\TokenManager;
use App\Domain\User\User;
use App\Infrastructure\Persistence\Eloquent\Models\User as UserEloquentModel;
use Illuminate\Support\Facades\Auth;

class LaravelTokenManager implements TokenManager
{

    public function create(User $user): string
    {
        $userEloquentModel = UserEloquentModel::query()
            ->where('id', $user->getId()->getValue())
            ->first();
        return $userEloquentModel->createToken('api_token')->plainTextToken;
    }

    public function delete(User $user): void
    {

        $authUser = Auth::user();

        if (!$authUser || $authUser->getAuthIdentifier() != $user->getId()->getValue()) {
            return;
        }

        $authUser->currentAccessToken()->delete();
    }
}
