<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use App\Shared\Exceptions\DomainException;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);

        $middleware->redirectTo(
            guests: '/',
            users: '/home'
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (DomainException $exception, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            return response()->json([
                'data' => null,
                'errors' => [
                    'message' => $exception->getMessage(),
                    'context' => $exception->context(),
                ],
            ], $exception->status());
        });
    })->create();

if (isset($_ENV['VERCEL'])) {
    $storagePath = '/tmp/storage';
    if (!is_dir($storagePath)) {
        mkdir($storagePath, 0755, true);
        mkdir($storagePath . '/framework', 0755, true);
        mkdir($storagePath . '/framework/views', 0755, true);
        mkdir($storagePath . '/framework/cache', 0755, true);
        mkdir($storagePath . '/framework/sessions', 0755, true);
        mkdir($storagePath . '/logs', 0755, true);
    }
    $app->useStoragePath($storagePath);
}

return $app;

