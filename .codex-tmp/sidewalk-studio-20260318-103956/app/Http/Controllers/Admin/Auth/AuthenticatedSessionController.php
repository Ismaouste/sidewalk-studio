<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Services\AdminOnboardingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    public function create(): Response|RedirectResponse
    {
        if (app(AdminOnboardingService::class)->needsOnboarding()) {
            return to_route('admin.onboarding.create');
        }

        if (Auth::check()) {
            return to_route('admin.settings.edit');
        }

        return Inertia::render('Admin/Auth/Login');
    }

    public function store(Request $request): RedirectResponse
    {
        if (app(AdminOnboardingService::class)->needsOnboarding()) {
            return to_route('admin.onboarding.create');
        }

        $credentials = $request->validate([
            'email' => ['required', 'email:rfc'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ]);

        if (! Auth::attempt(
            $request->only('email', 'password'),
            (bool) ($credentials['remember'] ?? false),
        )) {
            return back()
                ->withErrors([
                    'email' => 'These credentials do not match our records.',
                ])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.settings.edit'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('admin.login');
    }
}
