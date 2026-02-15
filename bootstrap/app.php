<?php

use App\Domain\Exceptions\DomainException;
use App\Domain\Exceptions\InsufficientFundsException;
use App\Domain\Exceptions\InvalidArgumentException;
use App\Domain\Exceptions\InvalidCredentialsException;
use App\Domain\Exceptions\InvalidGameTypeException;
use App\Domain\Exceptions\UserAlreadyExistsException;
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
        $exceptions->renderable(function (DomainException $exception, Request $request) {
            if ($request->wantsJson() || $request->is('api/*')) {
                $status = match(true) {
                    $exception instanceof UserAlreadyExistsException,
                        $exception instanceof InvalidArgumentException,
                        $exception instanceof InsufficientFundsException,
                        $exception instanceof InvalidGameTypeException => 422,
                    $exception instanceof InvalidCredentialsException => 401,
                    default => 400,
                };

                abort($status, $exception->getMessage());
            }
        });
    })->create();
