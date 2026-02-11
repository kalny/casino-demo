<?php

use App\Exceptions\BusinessException;
use App\Exceptions\NotFoundException;
use App\Services\Auth\Exceptions\InvalidCredentialsException;
use App\Services\Game\Exceptions\InsufficientFundsException;
use App\Services\Game\Exceptions\InvalidConfigException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

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
        $exceptions->renderable(function (BusinessException $exception, Request $request) {
            if ($request->wantsJson() || $request->is('api/*')) {
                $status = match(true) {
                    $exception instanceof InvalidCredentialsException => 401,
                    $exception instanceof InsufficientFundsException => 402,
                    $exception instanceof NotFoundException => 404,
                    $exception instanceof InvalidConfigException => 500,
                    default => 400,
                };

                abort($status, $exception->getUserMessage());
            }
        });
    })->create();
