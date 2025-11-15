<?php



use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Tymon\JWTAuth\Http\Middleware\Authenticate;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            DisableBladeIconComponents::class,
            DispatchServingFilamentEvent::class,
        ]);

        $middleware->validateCsrfTokens();
    })
    ->withProviders([
        ...require __DIR__ . '/providers.php',
        \Tymon\JWTAuth\Providers\LaravelServiceProvider::class, // ← ДОБАВЬ!
    ])
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
