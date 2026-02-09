<?php

use App\Exceptions\BusinessException;
use App\Exceptions\NotFoundException;
use App\Services\Auth\Exceptions\InvalidCredentialsException;
use App\Services\Game\Exceptions\InsufficientFundsException;
use App\Services\Game\Exceptions\InvalidConfigException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (BusinessException $exception) {
            if ($exception instanceof InvalidCredentialsException) {
                abort(401, $exception->getUserMessage());
            }

            if ($exception instanceof NotFoundException) {
                abort(404, $exception->getUserMessage());
            }

            if ($exception instanceof InsufficientFundsException) {
                abort(402, $exception->getUserMessage());
            }

            if ($exception instanceof InvalidConfigException) {
                abort(500, $exception->getUserMessage());
            }
        });
    })->create();
