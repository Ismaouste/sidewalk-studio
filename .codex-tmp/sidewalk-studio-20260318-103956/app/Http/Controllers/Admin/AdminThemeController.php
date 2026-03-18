<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminAuditLogService;
use App\Services\PublishingService;
use App\Services\SiteSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminThemeController extends Controller
{
    public function __construct(
        protected SiteSettingsService $siteSettings,
        protected PublishingService $publishing,
        protected AdminAuditLogService $auditLogs,
    ) {}

    public function edit(): Response
    {
        $settings = $this->siteSettings->current()->toPersistenceArray();

        return Inertia::render('Admin/Theme/Edit', [
            'themeSettings' => $settings['theme_settings'],
            'staticExportSettings' => $settings['static_export_settings'],
            'publishingState' => $settings['publishing_state'],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'theme_settings' => ['required', 'array'],
            'theme_settings.default_theme' => ['required', 'string', 'in:morning,sunset'],
            'theme_settings.gradient_angle' => ['required', 'integer', 'between:0,360'],
            'theme_settings.surface_blur' => ['required', 'integer', 'between:0,40'],
            'theme_settings.line_thickness' => ['required', 'integer', 'between:1,8'],
            'static_export_settings' => ['required', 'array'],
            'static_export_settings.static_mode_enabled' => ['required', 'boolean'],
            'static_export_settings.github_pages_enabled' => ['required', 'boolean'],
            'static_export_settings.preprod_mode_enabled' => ['required', 'boolean'],
            'static_export_settings.export_base_path' => ['required', 'string', 'max:120'],
            'static_export_settings.export_output_path' => ['required', 'string', 'max:255'],
        ]);

        $settings = $this->siteSettings->current()->toPersistenceArray();
        $settings['theme_settings'] = $payload['theme_settings'];
        $settings['static_export_settings'] = $payload['static_export_settings'];
        $this->siteSettings->store($this->siteSettings->hydrate($settings));
        $this->siteSettings->markRebuildRequired('Theme or static export settings changed.');

        $this->auditLogs->recordAction(
            action: 'theme_settings.saved',
            subject: 'site_settings',
            summary: [
                'theme' => $payload['theme_settings']['default_theme'],
                'static_mode_enabled' => $payload['static_export_settings']['static_mode_enabled'],
            ],
            actor: $request->user() instanceof User ? $request->user() : null,
        );

        return to_route('admin.theme.edit')
            ->with('status', 'Theme and static export settings saved.');
    }

    public function rebuild(Request $request): RedirectResponse
    {
        $result = $this->publishing->rebuild();

        $this->auditLogs->recordAction(
            action: 'publishing.rebuild',
            subject: 'static_export',
            summary: [
                'ran_static_export' => $result['ran'],
            ],
            actor: $request->user() instanceof User ? $request->user() : null,
        );

        return to_route('admin.theme.edit')
            ->with('status', $result['output'] === 'No rebuild was needed.'
                ? 'No rebuild was needed.'
                : ($result['ran']
                    ? 'Rebuild completed and static export regenerated.'
                    : 'Rebuild completed. Static export mode is disabled.'));
    }
}
