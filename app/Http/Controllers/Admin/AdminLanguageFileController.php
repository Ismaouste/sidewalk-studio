<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminAuditLogService;
use App\Services\LanguageFileService;
use App\Services\SiteSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminLanguageFileController extends Controller
{
    public function __construct(
        protected LanguageFileService $languageFiles,
        protected SiteSettingsService $siteSettings,
        protected AdminAuditLogService $auditLogs,
    ) {}

    public function index(): Response
    {
        return Inertia::render('Admin/LanguageFiles/Index', [
            'files' => $this->languageFiles->all()->all(),
        ]);
    }

    public function update(Request $request, string $key): RedirectResponse
    {
        $payload = $request->validate([
            'payload' => ['required', 'array'],
        ]);

        $this->languageFiles->save($key, $payload['payload']);
        $this->siteSettings->forget();
        $this->siteSettings->markRebuildRequired("Language file {$key} changed.");
        $this->auditLogs->recordAction(
            action: 'language_file.saved',
            subject: 'language_file',
            summary: ['key' => $key],
            actor: $request->user() instanceof User ? $request->user() : null,
        );

        return to_route('admin.language-files.index')
            ->with('status', 'Language file saved.');
    }
}
