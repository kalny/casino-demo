<?php

namespace App\Providers;

use App\Services\Game\GameResolver;
use App\Services\Game\Contracts\GameFactory;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    public $bindings = [
        GameFactory::class => GameResolver::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
