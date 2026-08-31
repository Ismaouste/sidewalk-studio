<?php

namespace App\Services;

use App\Content\ContentPreview;
use App\Content\ContentSource;
use App\Content\Schema\PageSchemas;
use App\Models\Page;
use App\Support\PublicLocale;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Spatie\YamlFrontMatter\YamlFrontMatter;

class PageContentRepository
{
    /**
     * @return array<string, mixed>
     */
    public function get(string $page, ?string $locale = null): array
    {
        $item = $this->publicPage($page, $locale ?? app()->getLocale());

        if ($item === null) {
            throw new RuntimeException("Missing page content file for [{$page}].");
        }

        return $item;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function adminList(?string $locale = null): array
    {
        $locales = $locale ? [$locale] : ['en', 'fr'];

        return collect($locales)
            ->flatMap(fn (string $entryLocale) => $this->all($entryLocale))
            ->sortBy(fn (array $page): string => $page['page_key'].'-'.$page['locale'])
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    public function adminFind(string $pageKey, string $locale): array
    {
        $page = $this->publicPage($pageKey, $locale);

        if ($page === null) {
            abort(404);
        }

        return $page;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function savePage(string $pageKey, string $locale, array $payload): array
    {
        $this->assertMatchesSchema(
            $pageKey,
            $this->asFrontmatter($payload),
            "the {$locale} version of [{$pageKey}]",
        );

        $record = Page::query()->updateOrCreate(
            ['page_key' => $pageKey, 'locale' => $locale],
            [
                'title' => $payload['title'] ?: null,
                'description' => $payload['description'] ?: null,
                'seo_title' => $payload['seo_title'],
                'seo_description' => $payload['seo_description'],
                'robots' => $payload['robots'] ?: 'index,follow',
                'canonical_url' => $payload['canonical_url'] ?: null,
                'open_graph_image' => $payload['open_graph_image'] ?: null,
                'payload' => $payload['payload'] ?? [],
                // Publishing settles the question the draft was asking.
                'draft_payload' => null,
                'draft_saved_at' => null,
                'source_path' => $payload['source_path'] ?? null,
                'source_driver' => 'database',
            ],
        );

        return $this->shapeDatabasePage($record);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    protected function all(string $locale): Collection
    {
        $filePages = collect($this->resolveKnownPageKeys())
            ->map(fn (string $page): ?array => $this->publicPage($page, $locale))
            ->filter()
            ->values();

        if (! Schema::hasTable('pages')) {
            return $filePages;
        }

        $dbOnly = Page::query()
            ->where('locale', $locale)
            ->get()
            ->map(fn (Page $record): array => $this->shapeDatabasePage($record))
            ->reject(fn (array $page): bool => $filePages->contains('page_key', $page['page_key']));

        return $filePages->merge($dbOnly)->values();
    }

    /**
     * One source wins outright; the other is a fallback for what it does not
     * hold. They are never merged field by field, in either direction.
     *
     * The admin used to see a different page from the public one: this method
     * did an `array_replace_recursive` with the file on top, so an operator
     * who saved an edit was shown the file's version back in the form on the
     * next load. Two readings of "what this page says" is one too many, and
     * the one the editor shows had better be the one it is about to
     * overwrite.
     *
     * A page assembled half from a row and half from a file is also a page
     * nobody wrote, and reviewing it means reading both.
     *
     * @return array<string, mixed>|null
     */
    protected function publicPage(string $page, string $locale): ?array
    {
        $resolved = ContentSource::databaseWins()
            ? ($this->loadDatabasePage($page, $locale) ?? $this->loadFilePage($page, $locale))
            : ($this->loadFilePage($page, $locale) ?? $this->loadDatabasePage($page, $locale));

        return ContentPreview::isRequested()
            ? $this->withDraft($page, $locale, $resolved)
            : $resolved;
    }

    /**
     * The draft, laid over the published page, for a signed-in operator who
     * asked for it.
     *
     * A draft holds the whole payload, so it replaces rather than merges: a
     * page half from a draft and half from what is published is a page nobody
     * wrote, which is the same reason the two sources are never merged either.
     *
     * @param  array<string, mixed>|null  $resolved
     * @return array<string, mixed>|null
     */
    protected function withDraft(string $page, string $locale, ?array $resolved): ?array
    {
        $draft = $this->loadDraft($page, $locale);

        if ($draft === null || $resolved === null) {
            return $resolved;
        }

        return [
            ...$resolved,
            ...$draft,
            'payload' => $draft,
            'source_driver' => 'draft',
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function loadDraft(string $page, string $locale): ?array
    {
        if (! Schema::hasTable('pages')) {
            return null;
        }

        $draft = Page::query()
            ->where('page_key', $page)
            ->where('locale', $locale)
            ->value('draft_payload');

        return is_array($draft) && $draft !== [] ? $draft : null;
    }

    /**
     * Stores an unpublished edit and returns nothing but the fact it worked.
     *
     * Saving a page clears its draft: once the edit is published there is
     * nothing left to preview, and a stale draft sitting behind
     * `?preview=1` would show an operator a version of the page that no
     * longer exists anywhere.
     *
     * @param  array<string, mixed>  $payload
     */
    public function saveDraft(string $pageKey, string $locale, array $payload): void
    {
        Page::query()->updateOrCreate(
            ['page_key' => $pageKey, 'locale' => $locale],
            [
                'draft_payload' => $this->asFrontmatter($payload),
                'draft_saved_at' => now(),
                ...$this->publishedColumnsFor($pageKey, $locale),
            ],
        );
    }

    /**
     * A draft may be the first thing ever written for a page key that has no
     * row yet, so `updateOrCreate` has to be able to fill the non-nullable
     * columns. It fills them from what is published, or from the seed.
     *
     * @return array<string, mixed>
     */
    protected function publishedColumnsFor(string $pageKey, string $locale): array
    {
        if (Page::query()->where('page_key', $pageKey)->where('locale', $locale)->exists()) {
            return [];
        }

        $seed = $this->loadFilePage($pageKey, $locale) ?? [
            'seo_title' => $pageKey,
            'seo_description' => '',
            'payload' => [],
        ];

        return [
            'seo_title' => $seed['seo_title'],
            'seo_description' => $seed['seo_description'],
            'payload' => $seed['payload'] ?? [],
        ];
    }

    /**
     * The frontmatter a payload would be, if it were written back to Markdown.
     *
     * The declaration describes the content file, where metadata and content
     * are one flat mapping. The database splits them — columns for the
     * metadata, a JSON payload for the rest — so validating a save means
     * putting them back together first.
     *
     * Empty optional metadata is dropped rather than sent through as `''`.
     * The form posts every field it renders whether or not the operator
     * filled it in, and an absent optional field and an empty one mean the
     * same thing here; keeping the empty string would make a saved page
     * differ in shape from the file it was seeded from, which is precisely
     * what the parity check exists to catch.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    protected function asFrontmatter(array $payload): array
    {
        $frontmatter = $payload['payload'] ?? [];

        foreach (['seo_title', 'seo_description'] as $field) {
            $frontmatter[$field] = (string) ($payload[$field] ?? '');
        }

        foreach (['title', 'description', 'robots', 'canonical_url', 'open_graph_image'] as $field) {
            $value = (string) ($payload[$field] ?? '');

            if ($value !== '') {
                $frontmatter[$field] = $value;
            }
        }

        return $frontmatter;
    }

    /**
     * How a payload fails its declaration, in the form the admin can show.
     *
     * `savePage()` throws on the same violations, and keeps doing so: it is
     * the last guard for callers that are not an HTTP request, the seeder
     * among them. But a 500 is a poor way to tell an operator that a field
     * holds the wrong kind of value, so the controller asks first and turns
     * the answer into a message beside the form.
     *
     * @param  array<string, mixed>  $payload
     * @return array<int, string>
     */
    public function declarationViolations(string $pageKey, array $payload): array
    {
        return PageSchemas::for($pageKey)->violations($this->asFrontmatter($payload));
    }

    /**
     * Blocks a save that would leave the two locales holding different
     * shapes, naming the field that differs.
     *
     * This is the runtime replacement for the review-time checklist, and it
     * is the answer to the risk the guided editor introduces: the two locales
     * are edited one at a time, because side-by-side does not fit a phone,
     * and sequential editing is exactly how `fr/experience.md` drifted from
     * its English counterpart in the first place.
     *
     * @param  array<string, mixed>  $payload
     * @return array<int, string>
     */
    public function localeShapeDifferences(string $pageKey, string $locale, array $payload): array
    {
        $schema = PageSchemas::for($pageKey);
        $candidate = $schema->shapeOf($this->asFrontmatter($payload));
        $differences = [];

        foreach (PublicLocale::supported() as $other) {
            if ($other === $locale) {
                continue;
            }

            $counterpart = $this->publicPage($pageKey, $other);

            if ($counterpart === null) {
                continue;
            }

            $otherShape = $schema->shapeOf($this->asFrontmatter($counterpart));

            foreach ($this->flattenShape($candidate) as $path => $shape) {
                $otherPart = $this->flattenShape($otherShape)[$path] ?? null;

                if ($otherPart !== $shape) {
                    $differences[] = "[{$path}] is {$shape} in {$locale} and "
                        .($otherPart === null ? 'absent' : $otherPart)." in {$other}.";
                }
            }
        }

        return $differences;
    }

    /**
     * @param  array<string, mixed>  $shape
     * @return array<string, string>
     */
    protected function flattenShape(array $shape, string $prefix = ''): array
    {
        $flat = [];

        foreach ($shape as $key => $value) {
            $path = $prefix === '' ? (string) $key : "{$prefix}.{$key}";

            if (is_array($value)) {
                $flat += $this->flattenShape($value, $path);

                continue;
            }

            $flat[$path] = (string) $value;
        }

        return $flat;
    }

    /**
     * The page as its Markdown file states it, whatever the live source is.
     *
     * This is the revert path. The Markdown stays on disk and the admin never
     * writes to it, so it remains the addressable seed after the database
     * becomes authoritative — which is what lets an operator put a page back
     * without a developer.
     *
     * It reverts to what the file says *now*, not to what it said at seed
     * time. That is the more useful of the two: the file is the reviewed,
     * versioned copy, and reverting to a snapshot of an older version of it
     * would restore something nobody could find in git.
     *
     * @return array<string, mixed>|null
     */
    public function seededPage(string $page, string $locale): ?array
    {
        return $this->loadFilePage($page, $locale);
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function loadFilePage(string $page, string $locale): ?array
    {
        $path = $this->resolvePath($page, $locale);

        if (! $path) {
            return null;
        }

        $document = YamlFrontMatter::parseFile($path);
        $matter = $document->matter();

        $this->assertMatchesSchema($page, $matter, $path);

        return [
            ...$matter,
            'page_key' => $page,
            'locale' => $locale,
            'title' => (string) ($matter['title'] ?? ''),
            'description' => (string) ($matter['description'] ?? ''),
            'robots' => (string) ($matter['robots'] ?? 'index,follow'),
            'canonical_url' => (string) ($matter['canonical_url'] ?? ''),
            'open_graph_image' => (string) ($matter['open_graph_image'] ?? ''),
            'payload' => collect($matter)
                ->except(['seo_title', 'seo_description', 'title', 'description', 'robots', 'canonical_url', 'open_graph_image'])
                ->all(),
            'source_path' => $path,
            'source_driver' => 'file',
        ];
    }

    /**
     * Two of the forty-four keys a page can hold used to be checked, and only
     * for presence: `seo_title` and `seo_description`. Four and a half per
     * cent. Everything else went into `payload` unread, which is how a
     * paragraph that YAML had resolved into a mapping travelled from a content
     * file to the body copy of /fr/projects without anything objecting.
     *
     * A page key with no declaration is not silently waved through either.
     * Adding a Markdown file is now a two-part change — the file and its
     * declaration — and that is the point rather than a friction to smooth
     * over.
     *
     * @param  array<string, mixed>  $matter
     */
    protected function assertMatchesSchema(string $page, array $matter, string $path): void
    {
        $violations = PageSchemas::for($page)->violations($matter);

        if ($violations === []) {
            return;
        }

        throw new RuntimeException(
            "Content in [{$path}] does not match the [{$page}] schema:".PHP_EOL
            .'  - '.implode(PHP_EOL.'  - ', $violations),
        );
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function loadDatabasePage(string $page, string $locale): ?array
    {
        if (! Schema::hasTable('pages')) {
            return null;
        }

        $record = Page::query()
            ->where('page_key', $page)
            ->where('locale', $locale)
            ->first();

        if (! $record instanceof Page) {
            return null;
        }

        return $this->shapeDatabasePage($record);
    }

    /**
     * @return array<string, mixed>
     */
    protected function shapeDatabasePage(Page $record): array
    {
        return [
            ...($record->payload ?? []),
            'page_key' => $record->page_key,
            'locale' => $record->locale,
            'title' => (string) ($record->title ?? ''),
            'description' => (string) ($record->description ?? ''),
            'seo_title' => $record->seo_title,
            'seo_description' => $record->seo_description,
            'robots' => $record->robots,
            'canonical_url' => (string) ($record->canonical_url ?? ''),
            'open_graph_image' => (string) ($record->open_graph_image ?? ''),
            'payload' => $record->payload ?? [],
            'source_path' => $record->source_path,
            'source_driver' => $record->source_driver,
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function resolveKnownPageKeys(): array
    {
        $directory = resource_path('content/pages/en');

        if (! File::isDirectory($directory)) {
            return [];
        }

        return collect(File::files($directory))
            ->filter(fn ($file) => $file->getExtension() === 'md')
            ->map(fn ($file): string => $file->getFilenameWithoutExtension())
            ->sort()
            ->values()
            ->all();
    }

    protected function resolvePath(string $page, string $locale): ?string
    {
        $candidates = [
            resource_path("content/pages/{$locale}/{$page}.md"),
            resource_path("content/pages/en/{$page}.md"),
        ];

        foreach ($candidates as $path) {
            if (File::exists($path)) {
                return $path;
            }
        }

        return null;
    }
}
