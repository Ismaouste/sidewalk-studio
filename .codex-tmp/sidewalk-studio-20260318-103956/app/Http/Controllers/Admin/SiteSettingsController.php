<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminAuditLogService;
use App\Services\SiteSettingsService;
use App\SiteSettings\InvalidSiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class SiteSettingsController extends Controller
{
    public function __construct(
        protected AdminAuditLogService $auditLogs,
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
        $payload = array_replace_recursive($this->siteSettings->current()->toPersistenceArray(), $request->only([
            'site_identity',
            'contact_details',
            'social_links',
            'seo_defaults',
            'consent_copy',
            'feature_toggles',
        ]));
        $before = $this->siteSettings->current()->toPersistenceArray();

        try {
            $settings = $this->siteSettings->hydrate($payload);
        } catch (InvalidSiteSettings $exception) {
            if ($exception->getPrevious() instanceof ValidationException) {
                throw $exception->getPrevious();
            }

            throw $exception;
        }

        DB::transaction(function () use ($before, $request, $settings): void {
            $this->siteSettings->store($settings);
            $this->auditLogs->recordSettingsUpdated(
                $request->user() instanceof User ? $request->user() : null,
                $before,
                $settings->toPersistenceArray(),
            );
        });

        $this->siteSettings->markRebuildRequired('Site settings changed.');

        return to_route('admin.settings.edit')
            ->with(
                'status',
                'Site settings saved. Public reads now use the updated payload and the change was added to the audit log.',
            );
    }
}
