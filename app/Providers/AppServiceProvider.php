<?php

namespace App\Providers;

use App\Services\Game\CasinoGameResolver;
use App\Services\Game\GameResolver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    public $bindings = [
        GameResolver::class => CasinoGameResolver::class,
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
