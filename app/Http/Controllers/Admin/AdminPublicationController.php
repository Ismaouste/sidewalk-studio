<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminAuditLogService;
use App\Services\ContentRepository;
use App\Services\SiteSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminPublicationController extends Controller
{
    public function __construct(
        protected ContentRepository $content,
        protected SiteSettingsService $siteSettings,
        protected AdminAuditLogService $auditLogs,
    ) {}

    public function index(): Response
    {
        return Inertia::render('Admin/Publications/Index', [
            'groups' => $this->content->adminIndex(),
            'typeSettings' => $this->content->publicationTypeSettings(),
        ]);
    }

    public function create(string $type): Response
    {
        abort_unless(in_array($type, ['note', 'journal', 'case_study'], true), 404);

        return Inertia::render('Admin/Publications/Edit', [
            'mode' => 'create',
            'publication' => [
                'original_publication_type' => $type,
                'original_locale' => app()->getLocale(),
                'original_slug' => '',
                'publication_type' => $type,
                'locale' => app()->getLocale(),
                'title' => '',
                'slug' => '',
                'summary' => '',
                'status' => 'draft',
                'published_at' => '',
                'updated_at' => '',
                'tags' => [],
                'seo_title' => '',
                'seo_description' => '',
                'robots' => 'index,follow',
                'canonical_url' => '',
                'featured_image' => '',
                'featured_image_alt' => '',
                'open_graph_image' => '',
                'featured_video' => '',
                'category' => $type === 'case_study' ? 'work' : 'journal',
                'accent_tone' => '',
                'body_markdown' => '',
                'metadata' => [
                    'client' => '',
                    'role' => '',
                    'stack' => [],
                    'outcomes' => [],
                ],
                'source_path' => null,
                'source_driver' => 'database',
            ],
        ]);
    }

    public function edit(string $type, string $locale, string $slug): Response
    {
        return Inertia::render('Admin/Publications/Edit', [
            'mode' => 'edit',
            'publication' => [
                'original_publication_type' => $type,
                'original_locale' => $locale,
                'original_slug' => $slug,
                ...$this->content->adminFind($type, $locale, $slug),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        return $this->persist($request, null, null, null);
    }

    public function update(Request $request, string $type, string $locale, string $slug): RedirectResponse
    {
        return $this->persist($request, $type, $locale, $slug);
    }

    public function updateTypeSettings(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'settings' => ['required', 'array'],
            'settings.*.type' => ['required', 'string', 'in:note,journal,case_study'],
            'settings.*.cta_label' => ['required', 'string', 'max:120'],
            'settings.*.cta_target' => ['required', 'string', 'max:255'],
            'settings.*.accent_color' => ['required', 'string', 'max:32'],
        ]);

        $this->content->savePublicationTypeSettings($payload['settings']);
        $this->siteSettings->markRebuildRequired('Publication presentation settings changed.');

        return to_route('admin.publications.index')
            ->with('status', 'Publication presentation settings saved.');
    }

    protected function persist(Request $request, ?string $type, ?string $locale, ?string $slug): RedirectResponse
    {
        $payload = $request->validate([
            'publication_type' => ['required', 'string', 'in:note,journal,case_study'],
            'locale' => ['required', 'string', 'in:en,fr'],
            'title' => ['required', 'string', 'max:160'],
            'slug' => ['required', 'string', 'max:160'],
            'summary' => ['required', 'string', 'max:320'],
            'status' => ['required', 'string', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
            'updated_at' => ['nullable', 'date'],
            'tags' => ['required', 'array'],
            'tags.*' => ['required', 'string', 'max:60'],
            'seo_title' => ['required', 'string', 'max:160'],
            'seo_description' => ['required', 'string', 'max:500'],
            'robots' => ['required', 'string', 'max:40'],
            'canonical_url' => ['nullable', 'string', 'max:255'],
            'featured_image' => ['nullable', 'string', 'max:255'],
            'featured_image_alt' => ['nullable', 'string', 'max:255'],
            'open_graph_image' => ['nullable', 'string', 'max:255'],
            'featured_video' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:64'],
            'accent_tone' => ['nullable', 'string', 'max:64'],
            'body_markdown' => ['required', 'string'],
            'metadata' => ['nullable', 'array'],
            'metadata.client' => ['nullable', 'string', 'max:160'],
            'metadata.role' => ['nullable', 'string', 'max:160'],
            'metadata.stack' => ['nullable', 'array'],
            'metadata.stack.*' => ['required', 'string', 'max:120'],
            'metadata.outcomes' => ['nullable', 'array'],
            'metadata.outcomes.*' => ['required', 'string', 'max:160'],
        ]);

        $payload['original_publication_type'] = $type ?? $payload['publication_type'];
        $payload['original_locale'] = $locale ?? $payload['locale'];
        $payload['original_slug'] = $slug ?? $payload['slug'];

        $saved = $this->content->savePublication($payload);
        $this->siteSettings->markRebuildRequired("Publication {$saved['slug']} changed.");
        $this->auditLogs->recordAction(
            action: 'publication.saved',
            subject: 'publication',
            summary: [
                'type' => $saved['publication_type'],
                'locale' => $saved['locale'],
                'slug' => $saved['slug'],
            ],
            actor: $request->user() instanceof User ? $request->user() : null,
        );

        return to_route('admin.publications.edit', [
            'type' => $saved['publication_type'],
            'locale' => $saved['locale'],
            'slug' => $saved['slug'],
        ])->with('status', 'Publication saved.');
    }
}
