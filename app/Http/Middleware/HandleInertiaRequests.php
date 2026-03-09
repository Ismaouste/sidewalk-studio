<?php

namespace App\Http\Middleware;

use App\Services\SiteSettingsService;
use App\Support\PublicLocale;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $settings = app(SiteSettingsService::class)->current();

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
                'navigation' => PublicLocale::navigation(app()->getLocale()),
                'author' => config('site.author'),
                'contact' => $settings->contactDetails->toArray(),
                'social' => $settings->socialLinks->toArray(),
                'themeSettings' => $settings->themeSettings->toArray(),
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
                ],
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
