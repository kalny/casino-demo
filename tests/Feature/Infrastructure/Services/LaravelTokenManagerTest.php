<?php

namespace Tests\Feature\Infrastructure\Services;

use App\Infrastructure\Persistence\Eloquent\Models\User;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentUserRepository;
use App\Infrastructure\Services\LaravelTokenManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class LaravelTokenManagerTest extends TestCase
{
    use RefreshDatabase;

    public function testCreate(): void
    {
        $userEloquentModel = User::factory()->create();
        $userRepository = new EloquentUserRepository();
        $user = $userRepository->getById($userEloquentModel->id);

        $laravelTokenManager = new LaravelTokenManager();

        $token = $laravelTokenManager->create($user);

        [$id, $plainToken] = explode('|', $token);
        $model = PersonalAccessToken::find($id);

        $this->assertTrue(
            hash_equals($model->token, hash('sha256', $plainToken))
        );
    }
}
