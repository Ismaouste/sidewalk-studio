<?php

namespace App\Http\Middleware;

use App\Services\SiteSettingsService;
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
            'site' => [
                'name' => $settings->siteIdentity->name,
                'tagline' => $settings->siteIdentity->tagline,
                'description' => $settings->siteIdentity->description,
                'locale' => config('site.locale'),
                'url' => config('site.url'),
                'navigation' => config('site.navigation'),
                'author' => config('site.author'),
                'contact' => $settings->contactDetails->toArray(),
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
