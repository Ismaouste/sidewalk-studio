<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminAuditLogService;
use App\Services\PageContentRepository;
use App\Services\SiteSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminPageController extends Controller
{
    public function __construct(
        protected PageContentRepository $pages,
        protected SiteSettingsService $siteSettings,
        protected AdminAuditLogService $auditLogs,
    ) {}

    public function index(): Response
    {
        return Inertia::render('Admin/Pages/Index', [
            'pages' => $this->pages->adminList(),
        ]);
    }

    public function edit(string $page, string $locale): Response
    {
        return Inertia::render('Admin/Pages/Edit', [
            'page' => $this->pages->adminFind($page, $locale),
        ]);
    }

    public function update(Request $request, string $page, string $locale): RedirectResponse
    {
        $payload = $request->validate([
            'title' => ['nullable', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:500'],
            'seo_title' => ['required', 'string', 'max:160'],
            'seo_description' => ['required', 'string', 'max:500'],
            'robots' => ['required', 'string', 'max:40'],
            'canonical_url' => ['nullable', 'string', 'max:255'],
            'open_graph_image' => ['nullable', 'string', 'max:255'],
            'payload' => ['required', 'array'],
        ]);

        $saved = $this->pages->savePage($page, $locale, $payload);
        $this->siteSettings->markRebuildRequired("Page {$page} ({$locale}) changed.");
        $this->auditLogs->recordAction(
            action: 'page.saved',
            subject: 'page',
            summary: [
                'page_key' => $page,
                'locale' => $locale,
            ],
            actor: $request->user() instanceof User ? $request->user() : null,
        );

        return to_route('admin.pages.edit', [
            'page' => $saved['page_key'],
            'locale' => $saved['locale'],
        ])->with('status', 'Page content saved.');
    }
}
