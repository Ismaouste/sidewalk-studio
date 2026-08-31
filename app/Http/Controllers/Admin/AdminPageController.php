<?php

namespace App\Http\Controllers\Admin;

use App\Content\ContentPreview;
use App\Content\Schema\PageSchemas;
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
            /**
             * The editor is generated from the same declaration the save path
             * validates against, so the form cannot offer a field the server
             * will reject, and a field cannot be added to the content model
             * without the form growing an input for it.
             */
            'schema' => PageSchemas::for($page)->toArray(),
            'metaFields' => PageSchemas::META_FIELDS,
            'hasSeed' => $this->pages->seededPage($page, $locale) !== null,
        ]);
    }

    /**
     * Puts a page back to what its Markdown says.
     *
     * This is what makes an authoritative database safe for someone who does
     * not write code. Before the precedence reversal, a bad edit was harmless
     * because the public site ignored it; now it is live, and the operator
     * needs a way back that is not a developer and a deploy.
     *
     * The Markdown is that way back. The admin never writes to it, so it
     * stays the reviewed, versioned copy of every page — and reverting is
     * saving it over the row, which leaves the audit log a record of what
     * happened rather than a hole where a row used to be.
     */
    public function revert(Request $request, string $page, string $locale): RedirectResponse
    {
        $seed = $this->pages->seededPage($page, $locale);

        if ($seed === null) {
            return back()->with(
                'status',
                "No Markdown seed exists for [{$page}] in [{$locale}], so there is nothing to revert to.",
            );
        }

        $this->pages->savePage($page, $locale, $seed);
        $this->siteSettings->markRebuildRequired("Page {$page} ({$locale}) reverted to its seed.");
        $this->auditLogs->recordAction(
            action: 'page.reverted',
            subject: 'page',
            summary: [
                'page_key' => $page,
                'locale' => $locale,
                'source_path' => $seed['source_path'] ?? null,
            ],
            actor: $request->user() instanceof User ? $request->user() : null,
        );

        return to_route('admin.pages.edit', ['page' => $page, 'locale' => $locale])
            ->with('status', 'Page reverted to its Markdown seed.');
    }

    /**
     * Saves the current edit as a draft and sends the operator to the page
     * itself, rendered from it.
     *
     * Deliberately not gated on the declaration or on locale parity. A
     * preview answers "does this look right", which is a question worth
     * asking about content that is still wrong — and refusing to show an
     * operator their own work until it validates would make the check an
     * obstacle rather than a guardrail. Publishing is where both apply.
     */
    public function preview(Request $request, string $page, string $locale): RedirectResponse
    {
        $payload = $request->validate([
            'title' => ['nullable', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:500'],
            'seo_title' => ['nullable', 'string', 'max:160'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'robots' => ['nullable', 'string', 'max:40'],
            'canonical_url' => ['nullable', 'string', 'max:255'],
            'open_graph_image' => ['nullable', 'string', 'max:255'],
            'payload' => ['required', 'array'],
        ]);

        $this->pages->saveDraft($page, $locale, $payload);

        return redirect(ContentPreview::url(
            PageSchemas::routeFor($page),
            $locale,
        ));
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

        /**
         * The two locales are edited one at a time — side by side does not fit
         * a phone — and sequential editing is exactly how `fr/experience.md`
         * came to differ from its English counterpart. So the save runs the
         * shape comparison against the other locale and refuses a difference,
         * naming the field. This converts the editor's biggest risk into the
         * feature's main guarantee.
         */
        $differences = [
            ...$this->pages->declarationViolations($page, $payload),
            ...$this->pages->localeShapeDifferences($page, $locale, $payload),
        ];

        if ($differences !== []) {
            /**
             * `back()->withErrors()` rather than a `ValidationException`.
             * These differences are not field validation — they are a
             * comparison against the declaration and against the other
             * locale — so the redirect says so directly rather than dressing
             * them as input errors.
             *
             * The size of what travels back is handled at the boundary:
             * `bootstrap/app.php` keeps `payload` out of the flashed input,
             * because sessions here are cookie-backed and a browser silently
             * drops a cookie over ~4KB. Nothing needs the old input anyway;
             * the form still holds what was typed.
             */
            return back()->withErrors(['payload' => $differences]);
        }

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
