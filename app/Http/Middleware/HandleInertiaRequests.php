<?php

namespace App\Http\Middleware;

use App\Services\LoaderQuoteService;
use App\Services\SiteSettingsService;
use App\Support\PublicLocale;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function urlResolver(): \Closure
    {
        return function (Request $request): string {
            $relativeUrl = Str::start(
                Str::after($request->fullUrl(), $request->getSchemeAndHttpHost()),
                '/',
            );

            [$pathPart, $fragmentPart] = array_pad(explode('#', $relativeUrl, 2), 2, null);
            [$pathname, $queryString] = array_pad(explode('?', $pathPart, 2), 2, null);

            if ($queryString === null) {
                return $relativeUrl;
            }

            $params = collect(explode('&', $queryString))
                ->filter()
                ->reject(fn (string $pair): bool => Str::startsWith($pair, 'path='))
                ->reject(fn (string $pair): bool => Str::startsWith($pair, 'lang='))
                ->values();

            $query = $params->isEmpty() ? '' : '?'.$params->implode('&');
            $fragment = $fragmentPart !== null ? '#'.$fragmentPart : '';

            return "{$pathname}{$query}{$fragment}";
        };
    }

    public function share(Request $request): array
    {
        $settings = app(SiteSettingsService::class)->current();
        $loaderQuotes = app(LoaderQuoteService::class)->activeForLocale(
            app()->getLocale(),
            $settings->themeSettings->defaultTheme,
        );

        $colophonQuote = empty($loaderQuotes)
            ? null
            : (function (array $pool): ?array {
                $picked = $pool[array_rand($pool)];

                return [
                    'text' => $picked['text'],
                    'author' => $picked['author'] ?? null,
                ];
            })($loaderQuotes);

        return [
            ...parent::share($request),
            'name' => $settings->siteIdentity->name,
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
                'status' => fn () => $request->session()->get('status'),
            ],
            'site' => [
                'name' => $settings->siteIdentity->name,
                'tagline' => $settings->siteIdentity->tagline,
                'description' => $settings->siteIdentity->description,
                'locale' => app()->getLocale(),
                'url' => config('site.url'),
                'navigation' => PublicLocale::navigation(
                    app()->getLocale(),
                    PublicLocale::pathForRequest($request),
                ),
                'author' => config('site.author'),
                'contact' => $settings->contactDetails->toArray(),
                'social' => $settings->socialLinks->toArray(),
                'branding' => $settings->brandingSettings->toArray(),
                'shell' => PublicLocale::shellCopy(app()->getLocale()),
                'languageSwitcher' => PublicLocale::switcher(
                    $request,
                    app()->getLocale(),
                    (string) $request->attributes->get(
                        'public_locale_preference',
                        app()->getLocale(),
                    ),
                ),
                'runtime' => [
                    'staticPreview' => $request->headers->get('X-Static-Preview') === '1',
                    'staticBasePath' => $request->headers->get('X-Static-Preview-Base'),
                    'preprodModeEnabled' => $settings->staticExportSettings->preprodModeEnabled,
                    'themeDefaults' => $settings->themeSettings->toArray(),
                    'loaderQuotes' => $loaderQuotes,
                ],
                'colophonQuote' => $colophonQuote,
            ],
            'consent' => [
                'mode' => config('consent.mode'),
                'driver' => config('consent.driver'),
                'cookieName' => config('consent.cookie_name'),
                'categories' => config('consent.categories'),
                'services' => config('consent.services'),
            ],
        ];
    }
}
