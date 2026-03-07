<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class ResolvePublicLocale
{
    public const COOKIE_NAME = 'sidewalk_locale';

    /**
     * @var array<int, string>
     */
    protected array $supportedLocales = ['en', 'fr'];

    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->shouldHandle($request)) {
            /** @var Response $response */
            $response = $next($request);

            return $response;
        }

        $locale = $this->resolveLocale($request);

        app()->setLocale($locale);
        $request->attributes->set('public_locale', $locale);

        if ($this->resolveSupportedLocale($request->query('lang')) !== null) {
            Cookie::queue(Cookie::make(
                self::COOKIE_NAME,
                $locale,
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

    protected function resolveLocale(Request $request): string
    {
        return $this->resolveSupportedLocale($request->query('lang'))
            ?? $this->resolveSupportedLocale($request->cookie(self::COOKIE_NAME))
            ?? $this->resolveBrowserLocale($request)
            ?? 'en';
    }

    protected function resolveBrowserLocale(Request $request): ?string
    {
        foreach ($request->getLanguages() as $language) {
            $locale = $this->resolveSupportedLocale($language);

            if ($locale !== null) {
                return $locale;
            }
        }

        return null;
    }

    protected function resolveSupportedLocale(mixed $candidate): ?string
    {
        if (! is_string($candidate) || $candidate === '') {
            return null;
        }

        $normalized = strtolower(trim(explode(',', $candidate)[0]));
        $language = explode('-', $normalized)[0];

        return in_array($language, $this->supportedLocales, true)
            ? $language
            : null;
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
