<?php

use App\Http\Middleware\AdminAuthenticate;
use App\Http\Middleware\CachePublicResponse;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\ResolvePublicLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        app_path('Console/Commands'),
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            ResolvePublicLocale::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
            CachePublicResponse::class,
        ]);

        $middleware->alias([
            'admin.auth' => AdminAuthenticate::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        /**
         * A refused save has to reach the operator.
         *
         * The handler answers a `ValidationException` by redirecting with the
         * errors and the whole request input beside them. Sessions here are
         * cookie-backed, a browser silently drops a cookie over ~4KB, and the
         * whole request input on an admin page form is a page — so the cookie
         * was dropped and took the errors with it. The save was correctly
         * refused and said nothing at all.
         *
         * `payload` is the key every editor in /admin posts its document
         * under, and it is the only one large enough to matter. Nothing is
         * lost by dropping it: no `old()` call and no `withInput()` call
         * exists in this application. The forms are Inertia forms, and an
         * Inertia form still holds what was typed.
         */
        $exceptions->dontFlash(['payload']);
    })->create();
