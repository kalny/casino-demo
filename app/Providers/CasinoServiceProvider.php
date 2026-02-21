<?php

namespace App\Providers;

use App\Application\Ports\TransactionManager;
use App\Domain\Games\Repository\GameOutcomeRepository;
use App\Domain\Games\Repository\GameRepository;
use App\Domain\Services\IdGenerator;
use App\Domain\Services\PasswordHasher;
use App\Domain\Services\RandomNumberGenerator;
use App\Domain\Services\TokenManager;
use App\Domain\User\Repository\UserRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentGameOutcomeRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentGameRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentUserRepository;
use App\Infrastructure\Persistence\LaravelTransactionManager;
use App\Infrastructure\Services\LaravelIdGenerator;
use App\Infrastructure\Services\LaravelPasswordHasher;
use App\Infrastructure\Services\LaravelTokenManager;
use App\Infrastructure\Services\PHPRandomNumberGenerator;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\ServiceProvider;

class CasinoServiceProvider extends ServiceProvider
{
    public $bindings = [
        UserRepository::class => EloquentUserRepository::class,
        PasswordHasher::class => LaravelPasswordHasher::class,
        TokenManager::class => LaravelTokenManager::class,
        GameRepository::class => EloquentGameRepository::class,
        GameOutcomeRepository::class => EloquentGameOutcomeRepository::class,
        TransactionManager::class => LaravelTransactionManager::class,
        RandomNumberGenerator::class => PHPRandomNumberGenerator::class,
        IdGenerator::class => LaravelIdGenerator::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Factory::guessFactoryNamesUsing(function (string $modelName) {
            $factoryNamespace = 'Database\\Factories\\';
            $modelBaseName = class_basename($modelName);
            return $factoryNamespace . $modelBaseName . 'Factory';
        });
    }
}
