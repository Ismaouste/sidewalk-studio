<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Services\AdminAuditLogService;
use App\Services\AdminOnboardingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class AdminOnboardingController extends Controller
{
    public function __construct(
        protected AdminOnboardingService $onboarding,
        protected AdminAuditLogService $auditLogs,
    ) {}

    public function create(): Response|RedirectResponse
    {
        if (! $this->onboarding->needsOnboarding()) {
            return Auth::check()
                ? to_route('admin.settings.edit')
                : to_route('admin.login');
        }

        return Inertia::render('Admin/Auth/Onboarding');
    }

    public function store(Request $request): RedirectResponse
    {
        if (! $this->onboarding->needsOnboarding()) {
            return to_route('admin.login');
        }

        $payload = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'password' => array_values(array_filter([
                'required',
                'confirmed',
                Password::defaults(),
            ])),
        ]);

        $user = $this->onboarding->complete($payload);

        Auth::login($user);
        $request->session()->regenerate();

        $this->auditLogs->recordAction(
            action: 'admin.onboarding.completed',
            subject: 'admin.operator',
            summary: [
                'email' => $user->email,
            ],
            actor: $user,
        );

        return to_route('admin.settings.edit')
            ->with('status', 'Onboarding completed. The first operator account is active and site settings were initialized.');
    }
}
