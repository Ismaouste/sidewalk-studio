<?php

namespace App\Http\Middleware;

use App\Services\AdminOnboardingService;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class AdminAuthenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        if (! $request->expectsJson()) {
            return app(AdminOnboardingService::class)->needsOnboarding()
                ? route('admin.onboarding.create')
                : route('admin.login');
        }

        return null;
    }
}
