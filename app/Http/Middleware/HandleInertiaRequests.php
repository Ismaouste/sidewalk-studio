<?php

namespace App\Http\Middleware;

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
        return [
            ...parent::share($request),
            'name' => config('site.name'),
            'auth' => [
                'user' => $request->user(),
            ],
            'site' => [
                'name' => config('site.name'),
                'tagline' => config('site.tagline'),
                'description' => config('site.description'),
                'locale' => config('site.locale'),
                'url' => config('site.url'),
                'navigation' => config('site.navigation'),
                'author' => config('site.author'),
                'contact' => config('site.contact'),
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
