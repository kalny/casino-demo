<?php

namespace Tests\Feature\Infrastructure\Infrastructure\Persistence;

use App\Infrastructure\Persistence\Eloquent\Models\User;
use App\Infrastructure\Persistence\LaravelTransactionManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaravelTransactionManagerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws \Throwable
     */
    public function testTransactional(): void
    {
        $laravelTransactionManager = new LaravelTransactionManager();

        $this->assertDatabaseCount('users', 0);

        $laravelTransactionManager->transactional(function () use ($laravelTransactionManager) {
            User::factory()->create();
        });

        $this->assertDatabaseCount('users', 1);
    }
}
