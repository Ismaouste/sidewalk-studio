<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminDashboardService;
use App\Services\AdminOnboardingService;
use App\Services\SiteSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

/**
 * The door into the back office.
 *
 * It used to redirect to Settings, which is the screen an operator needs
 * least often and which answers no question they arrived with. It now renders
 * what is unfinished, because that is the question they did arrive with.
 *
 * The unauthenticated branches stay exactly as they were: this route sits
 * outside the `admin.auth` group precisely so a first run can be sent to
 * onboarding rather than to a login form for an account that does not exist.
 */
class AdminEntryController extends Controller
{
    public function __invoke(
        AdminOnboardingService $onboarding,
        AdminDashboardService $dashboard,
        SiteSettingsService $siteSettings,
    ): RedirectResponse|Response {
        if ($onboarding->needsOnboarding()) {
            return to_route('admin.onboarding.create');
        }

        if (! Auth::check()) {
            return to_route('admin.login');
        }

        return Inertia::render('Admin/Dashboard', [
            ...$dashboard->digest(),
            'rebuildRequired' => $siteSettings->current()->publishingState->rebuildRequired,
        ]);
    }
}
