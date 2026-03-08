<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CachePublicResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        if (! $request->isMethod('GET')) {
            return $response;
        }

        if ($request->is('admin') || $request->is('admin/*')) {
            return $response;
        }

        if ($response->isRedirection()) {
            return $response;
        }

        $response->headers->set(
            'Cache-Control',
            'public, max-age=900, s-maxage=31536000, stale-while-revalidate=86400'
        );

        return $response;
    }
}
