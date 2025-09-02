<?php

use App\Exceptions\HttpRenderableException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api/v1',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->reportable(function (HttpRenderableException $exception): void {
            Log::error($exception->errorIdentifier()->value, [
                'error' => $exception->getMessage(),
            ]);
        });

        $exceptions->renderable(function (HttpRenderableException $exception) {
            return response()->json([
                'identifier' => $exception->errorIdentifier()->value,
                'message' => $exception->userMessage(),
            ], $exception->httpStatus()->value);
        });
    })->create();
