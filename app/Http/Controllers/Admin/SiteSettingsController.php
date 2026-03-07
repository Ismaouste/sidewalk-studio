<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SiteSettingsService;
use App\SiteSettings\InvalidSiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class SiteSettingsController extends Controller
{
    public function __construct(
        protected SiteSettingsService $siteSettings,
    ) {}

    public function edit(): Response
    {
        return Inertia::render('Admin/Settings/Edit', [
            'settings' => $this->siteSettings->current()->toPersistenceArray(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $payload = $request->only([
            'site_identity',
            'contact_details',
            'social_links',
            'seo_defaults',
            'consent_copy',
            'feature_toggles',
        ]);

        try {
            $this->siteSettings->update($payload);
        } catch (InvalidSiteSettings $exception) {
            if ($exception->getPrevious() instanceof ValidationException) {
                throw $exception->getPrevious();
            }

            throw $exception;
        }

        return to_route('admin.settings.edit')
            ->with('status', 'Site settings updated.');
    }
}
