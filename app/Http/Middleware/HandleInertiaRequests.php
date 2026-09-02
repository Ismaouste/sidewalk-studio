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
                'repositoryUrl' => config('site.repository_url'),
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
                'audience' => [
                    'enabled' => (bool) config('audience.enabled'),
                    'endpoint' => (string) config('audience.endpoint'),
                ],
            ],
        ];
    }

    /**
     * `payload` keeps all of its violations; every other key keeps one.
     *
     * A page save is refused by comparing a whole document against its
     * declaration and against the other locale, so one refusal routinely
     * names several fields. Inertia hands the client `$errors[0]` per key
     * unless `$withAllErrors` is set — and that switch is application-wide,
     * while every other admin form here is written against a string. So the
     * eight reasons a save was refused reached the browser as one, and the
     * operator fixed them one save at a time, learning each only after
     * fixing the last.
     *
     * Narrowing it to the one key that carries a list keeps the other forms
     * untouched. `Admin/Pages/Edit.vue` was already written for the array it
     * could never receive.
     *
     * @return object
     */
    public function resolveValidationErrors(Request $request)
    {
        $resolved = parent::resolveValidationErrors($request);

        if (! $request->hasSession() || ! $request->session()->has('errors')) {
            return $resolved;
        }

        if (! property_exists($resolved, 'payload')) {
            return $resolved;
        }

        $messages = $request->session()->get('errors')->getBag('default')->get('payload');

        if ($messages !== []) {
            $resolved->payload = $messages;
        }

        return $resolved;
    }
}
