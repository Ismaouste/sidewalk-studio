<?php

namespace App\Http\Middleware;

use App\Support\PublicLocale;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class ResolvePublicLocale
{
    public const COOKIE_NAME = PublicLocale::COOKIE_NAME;

    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->shouldHandle($request)) {
            /** @var Response $response */
            $response = $next($request);

            return $response;
        }

        $preferredLocale = PublicLocale::preferredLocaleForRequest($request);
        $locale = PublicLocale::isAvailableForRequest($request, $preferredLocale)
            ? $preferredLocale
            : PublicLocale::default();

        app()->setLocale($locale);
        $request->attributes->set('public_locale', $locale);
        $request->attributes->set('public_locale_preference', $preferredLocale);

        if (
            PublicLocale::resolveSupportedLocale($request->route('locale')) !== null ||
            PublicLocale::resolveSupportedLocale($request->query('lang')) !== null
        ) {
            Cookie::queue(Cookie::make(
                self::COOKIE_NAME,
                $preferredLocale,
                60 * 24 * 365,
                '/',
                null,
                $request->isSecure(),
                false,
                false,
                'lax',
            ));
        }

        /** @var Response $response */
        $response = $next($request);
        $response->headers->set('Content-Language', $locale);
        $response->headers->set('Vary', $this->mergeVary($response->headers->get('Vary')));

        return $response;
    }

    protected function shouldHandle(Request $request): bool
    {
        return ! $request->is(
            'admin',
            'admin/*',
            'cv/*',
            'robots.txt',
            'sitemap.xml',
            'storage/*',
            'up',
        );
    }

    protected function mergeVary(?string $current): string
    {
        return collect(explode(',', (string) $current))
            ->map(fn (string $value): string => trim($value))
            ->filter()
            ->merge(['Accept-Language', 'Cookie'])
            ->unique()
            ->implode(', ');
    }
}
