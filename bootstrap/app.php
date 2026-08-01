<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Xendit calls the webhook without any CSRF/session context, and
        // the ESP32 firmware never sends a CSRF token, so /api/* routes
        // are already excluded from CSRF by Laravel's default `api`
        // middleware group. No additional exclusions are required here.
        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Paksa matikan debug handler jika environment production
        $exceptions->render(function (Throwable $e, $request) {
            if (config('app.env') === 'production' && !config('app.debug')) {
                // Biarkan Laravel merender halaman error standar (bukan Whoops debug screen)
                return null; 
            }
        });
    })->create();