<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminOnboardingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class AdminEntryController extends Controller
{
    public function __invoke(AdminOnboardingService $onboarding): RedirectResponse
    {
        if ($onboarding->needsOnboarding()) {
            return to_route('admin.onboarding.create');
        }

        if (Auth::check()) {
            return to_route('admin.settings.edit');
        }

        return to_route('admin.login');
    }
}
