<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminAuditLogService;
use App\Services\SiteSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;
use Inertia\Response;

class AdminBrandingController extends Controller
{
    public function __construct(
        protected SiteSettingsService $siteSettings,
        protected AdminAuditLogService $auditLogs,
    ) {}

    public function edit(): Response
    {
        return Inertia::render('Admin/Branding/Edit', [
            'brandingSettings' => $this->siteSettings->current()->brandingSettings->toArray(),
            'fallbackVariants' => [
                ['value' => 'avatar', 'label' => 'Avatar fallback'],
                ['value' => 'monogram_circle', 'label' => 'Circle monogram'],
                ['value' => 'monogram_square', 'label' => 'Square monogram'],
                ['value' => 'wordmark', 'label' => 'Wordmark'],
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'branding_settings' => ['required', 'array'],
            'branding_settings.asset_mode' => ['required', 'string', 'in:uploaded,fallback'],
            'branding_settings.uploaded_asset_path' => ['nullable', 'string', 'max:255'],
            'branding_settings.fallback_variant' => ['required', 'string', 'in:avatar,monogram_circle,monogram_square,wordmark'],
            'branding_settings.fallback_label' => ['required', 'string', 'max:64'],
            'branding_settings.fallback_subtitle' => ['required', 'string', 'max:120'],
            'branding_settings.active_alt' => ['required', 'string', 'max:160'],
            'branding_upload' => ['nullable', 'image', 'max:4096'],
        ]);

        $settings = $this->siteSettings->current()->toPersistenceArray();
        $settings['branding_settings'] = $payload['branding_settings'];

        if ($request->hasFile('branding_upload')) {
            $file = $request->file('branding_upload');
            $directory = public_path('uploads/branding');
            File::ensureDirectoryExists($directory);

            $filename = now()->format('YmdHis').'-'.$file->hashName();
            $file->move($directory, $filename);
            $settings['branding_settings']['uploaded_asset_path'] = '/uploads/branding/'.$filename;
            $settings['branding_settings']['asset_mode'] = 'uploaded';
        }

        $this->siteSettings->store($this->siteSettings->hydrate($settings));
        $this->siteSettings->markRebuildRequired('Branding settings changed.');

        $this->auditLogs->recordAction(
            action: 'branding.saved',
            subject: 'site_settings',
            summary: [
                'asset_mode' => $settings['branding_settings']['asset_mode'],
                'fallback_variant' => $settings['branding_settings']['fallback_variant'],
            ],
            actor: $request->user() instanceof User ? $request->user() : null,
        );

        return to_route('admin.branding.edit')
            ->with('status', 'Branding settings saved.');
    }
}
