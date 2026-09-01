<?php

use App\Http\Middleware\AdminAuthenticate;
use App\Http\Middleware\CachePublicResponse;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\ResolvePublicLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;

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

        /**
         * A content document is not a form field.
         *
         * `ConvertEmptyStringsToNull` is in Laravel's default global stack —
         * it is written nowhere in this file, which is most of why this took
         * a browser to find. It exists so `?q=` reads as absent, and it walks
         * nested arrays to do it. The editors in /admin do not post fields;
         * they post a document, and every `''` in that document, at any
         * depth, arrived as `null`.
         *
         * The declaration is right to refuse null for a required line, so the
         * refusal was correct and the request was wrong. Four of the sixteen
         * page/locale pairs could not be saved at all — `experience` holds
         * eight empty strings across its two widget groups, `contact` one —
         * and an identity save, changing nothing, was refused. The database
         * stores `''` correctly; only the round trip lost it, which is why
         * every test calling the repository directly stayed green.
         *
         * Scoped by path rather than by route: global middleware runs before
         * the router, so `routeIs()` is always false here. `payload` is the
         * key every editor in /admin posts its document under.
         *
         * The metadata columns are unaffected. `PageContentRepository::savePage`
         * writes them through `?:`, so an empty title still lands as null.
         */
        $middleware->convertEmptyStringsToNull(except: [
            fn (Request $request): bool => $request->is('admin/*')
                && $request->has('payload'),
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
