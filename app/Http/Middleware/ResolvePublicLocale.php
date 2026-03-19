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

        if ($this->hasLegacyPublicQuery($request)) {
            $response = redirect()->to(
                $this->canonicalPublicUrl($request, $preferredLocale),
                301,
            );
            $this->queueLocaleCookie($request, $preferredLocale);

            return $response;
        }

        if (
            PublicLocale::resolveSupportedLocale($request->route('locale')) !== null ||
            PublicLocale::resolveSupportedLocale($request->query('lang')) !== null
        ) {
            $this->queueLocaleCookie($request, $preferredLocale);
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
            'content-visuals/*',
            'robots.txt',
            'sitemap.xml',
            'storage/*',
            'up',
        );
    }

    protected function hasLegacyPublicQuery(Request $request): bool
    {
        if ($request->query->has('lang')) {
            return true;
        }

        if (! $request->query->has('path')) {
            return false;
        }

        return ! $this->isVercelRewritePathQuery($request);
    }

    protected function isVercelRewritePathQuery(Request $request): bool
    {
        return collect([
            'x-vercel-id',
            'x-matched-path',
            'x-vercel-deployment-url',
        ])->contains(
            fn (string $header): bool => filled($request->headers->get($header)),
        );
    }

    protected function canonicalPublicUrl(Request $request, string $preferredLocale): string
    {
        $routeLocale = PublicLocale::resolveSupportedLocale($request->route('locale'));
        $targetLocale = $routeLocale ?? $preferredLocale;
        $path = PublicLocale::localizedPath(
            PublicLocale::pathForRequest($request),
            $targetLocale,
        );
        $query = collect($request->query())
            ->except(['lang', 'path'])
            ->filter(fn (mixed $value): bool => $value !== null && $value !== '')
            ->all();

        if ($query === []) {
            return $path;
        }

        return sprintf('%s?%s', $path, http_build_query($query));
    }

    protected function queueLocaleCookie(Request $request, string $preferredLocale): void
    {
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
