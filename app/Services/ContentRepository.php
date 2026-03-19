<?php

namespace App\Services;

use App\Models\Publication;
use App\Models\PublicationTypeSetting;
use App\Support\ContentVisual;
use App\Support\PublicLocale;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use RuntimeException;
use Spatie\YamlFrontMatter\YamlFrontMatter;

class ContentRepository
{
    public function __construct(
        protected ManagedMarkdownFileService $markdownFiles,
    ) {}

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function all(string $section, ?string $locale = null, bool $includeFallback = true): Collection
    {
        $requestedLocale = $locale ?? app()->getLocale();
        $items = $this->mergedItems($section, $requestedLocale, $includeFallback);

        return $items->sort(function (array $left, array $right) use ($requestedLocale): int {
            $leftPriority = $left['locale'] === $requestedLocale ? 0 : 1;
            $rightPriority = $right['locale'] === $requestedLocale ? 0 : 1;

            if ($leftPriority !== $rightPriority) {
                return $leftPriority <=> $rightPriority;
            }

            $leftSourcePriority = $left['source_driver'] === 'file' ? 0 : 1;
            $rightSourcePriority = $right['source_driver'] === 'file' ? 0 : 1;

            if ($leftSourcePriority !== $rightSourcePriority) {
                return $leftSourcePriority <=> $rightSourcePriority;
            }

            return strcmp($right['published_at'] ?: '', $left['published_at'] ?: '');
        })
            ->unique(fn (array $item): string => "{$item['section']}:{$item['slug']}")
            ->values();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function published(string $section, ?string $locale = null, bool $includeFallback = true): Collection
    {
        return $this->all($section, $locale, $includeFallback)
            ->where('status', 'published')
            ->values();
    }

    /**
     * @param  array<int, string>  $sections
     * @return Collection<int, array<string, mixed>>
     */
    public function feed(array $sections, array $filters = []): Collection
    {
        $items = collect($sections)
            ->flatMap(fn (string $section) => $this->published(
                $section,
                $filters['locale'] ?? app()->getLocale(),
                (bool) ($filters['include_fallback'] ?? false),
            ))
            ->sortByDesc(fn (array $item) => $item['published_at'])
            ->values();

        if (! empty($filters['tag'])) {
            $items = $items->filter(
                fn (array $item): bool => in_array((string) $filters['tag'], $item['tags'], true),
            )->values();
        }

        if (! empty($filters['category'])) {
            $items = $items->where('category', (string) $filters['category'])->values();
        }

        if (! empty($filters['publication_type'])) {
            $items = $items->where('publication_type', (string) $filters['publication_type'])->values();
        }

        if (! empty($filters['limit'])) {
            $items = $items->take((int) $filters['limit'])->values();
        }

        return $items->values();
    }

    /**
     * @return array<string, mixed>
     */
    public function findPublished(string $section, string $slug, ?string $locale = null): array
    {
        $item = $this->published($section, $locale)
            ->firstWhere('slug', $slug);

        if (! $item) {
            abort(404);
        }

        return $item;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function adminIndex(?string $locale = null): array
    {
        $locales = $locale ? [$locale] : ['en', 'fr'];
        $items = collect($locales)
            ->flatMap(fn (string $entryLocale) => $this->mergedAllTypes($entryLocale, false))
            ->sortByDesc(fn (array $item) => $item['published_at'] ?: '0000-00-00')
            ->values();

        return [
            'note' => $items->where('publication_type', 'note')->values()->all(),
            'journal' => $items->where('publication_type', 'journal')->values()->all(),
            'case_study' => $items->where('publication_type', 'case_study')->values()->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function adminFind(string $type, string $locale, string $slug): array
    {
        $record = $this->findRecord($type, $locale, $slug);

        if ($record instanceof Publication) {
            return $this->shapeDatabaseRecord($record);
        }

        $item = $this->mergedAllTypes($locale, true)
            ->first(fn (array $item): bool => $item['publication_type'] === $type && $item['slug'] === $slug);

        if (! $item) {
            abort(404);
        }

        return $item;
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function publicationTypeSettings(): array
    {
        $defaults = collect([
            [
                'type' => 'note',
                'cta_label' => 'Browse notes',
                'cta_target' => '/journal?type=note',
                'accent_color' => 'dominant',
            ],
            [
                'type' => 'journal',
                'cta_label' => 'Open the journal',
                'cta_target' => '/journal',
                'accent_color' => 'green',
            ],
            [
                'type' => 'case_study',
                'cta_label' => 'View case studies',
                'cta_target' => '/case-studies',
                'accent_color' => 'sun',
            ],
        ])->keyBy('type');

        if (! Schema::hasTable('publication_type_settings')) {
            return $defaults->values()->all();
        }

        $persisted = PublicationTypeSetting::query()->get()
            ->map(fn (PublicationTypeSetting $setting): array => [
                'type' => $setting->type,
                'cta_label' => $setting->cta_label,
                'cta_target' => $setting->cta_target,
                'accent_color' => $setting->accent_color,
            ])
            ->keyBy('type');

        return $defaults->merge($persisted)->values()->all();
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function savePublication(array $payload): array
    {
        $record = $this->findRecord(
            $payload['original_publication_type'] ?? $payload['publication_type'],
            $payload['original_locale'] ?? $payload['locale'],
            $payload['original_slug'] ?? $payload['slug'],
        );

        if (! $record instanceof Publication) {
            $record = new Publication();
        }

        $sourcePath = $this->normalizeSourcePath(
            $payload['source_path'] ?? null,
            $payload['publication_type'],
            $payload['locale'],
            $payload['slug'],
        );
        $existingPath = is_string($record->source_path) ? $record->source_path : null;
        $existingMatter = $existingPath && File::exists($existingPath)
            ? $this->markdownFiles->read($existingPath)['matter']
            : [];

        $this->markdownFiles->write(
            $sourcePath,
            $this->publicationFrontmatter($payload, $existingMatter),
            (string) $payload['body_markdown'],
        );

        if ($existingPath && $existingPath !== $sourcePath && File::exists($existingPath)) {
            File::delete($existingPath);
        }

        $record->fill([
            'type' => $payload['publication_type'],
            'locale' => $payload['locale'],
            'slug' => $payload['slug'],
            'title' => $payload['title'],
            'summary' => $payload['summary'],
            'body_markdown' => '',
            'status' => $payload['status'],
            'published_at' => $payload['published_at'] ?: null,
            'updated_at_publication' => $payload['updated_at'] ?: null,
            'tags' => $payload['tags'],
            'seo_title' => $payload['seo_title'],
            'seo_description' => $payload['seo_description'],
            'robots' => $payload['robots'] ?: 'index,follow',
            'canonical_url' => $payload['canonical_url'] ?: null,
            'featured_image' => $payload['featured_image'] ?: null,
            'featured_image_alt' => $payload['featured_image_alt'] ?: null,
            'open_graph_image' => $payload['open_graph_image'] ?: null,
            'featured_video' => $payload['featured_video'] ?: null,
            'category' => $payload['category'] ?: null,
            'accent_tone' => $payload['accent_tone'] ?: null,
            'metadata' => $payload['metadata'] ?? [],
            'source_path' => $sourcePath,
            'source_driver' => 'hybrid',
        ]);
        $record->save();

        return $this->shapeDatabaseRecord($record);
    }

    /**
     * @param  array<string, mixed>  $item
     */
    public function importPublication(array $item): void
    {
        if (! Schema::hasTable('publications')) {
            return;
        }

        Publication::query()->updateOrCreate(
            [
                'type' => $item['publication_type'],
                'locale' => $item['locale'],
                'slug' => $item['slug'],
            ],
            [
                'title' => $item['title'],
                'summary' => $item['summary'],
                'body_markdown' => '',
                'status' => $item['status'],
                'published_at' => $item['published_at'] ?: null,
                'updated_at_publication' => $item['updated_at'] ?: null,
                'tags' => $item['tags'],
                'seo_title' => $item['seo_title'],
                'seo_description' => $item['seo_description'],
                'robots' => $item['robots'] ?: 'index,follow',
                'canonical_url' => $item['canonical_url'] ?: null,
                'featured_image' => $item['featured_image'] ?: null,
                'featured_image_alt' => $item['featured_image_alt'] ?: null,
                'open_graph_image' => $item['open_graph_image'] ?: null,
                'featured_video' => $item['featured_video'] ?: null,
                'category' => $item['category'] ?: null,
                'accent_tone' => $item['accent_tone'] ?: null,
                'metadata' => $item['metadata'] ?? [],
                'source_path' => $item['source_path'],
                'source_driver' => 'hybrid',
            ],
        );
    }

    /**
     * @param  array<int, array<string, string>>  $settings
     */
    public function savePublicationTypeSettings(array $settings): void
    {
        if (! Schema::hasTable('publication_type_settings')) {
            return;
        }

        foreach ($settings as $setting) {
            PublicationTypeSetting::query()->updateOrCreate(
                ['type' => $setting['type']],
                [
                    'cta_label' => $setting['cta_label'],
                    'cta_target' => $setting['cta_target'],
                    'accent_color' => $setting['accent_color'],
                ],
            );
        }
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    protected function mergedItems(string $section, string $locale, bool $includeFallback): Collection
    {
        $databaseItems = $this->databaseItemsForSection($section, $locale, $includeFallback);
        $fileItems = $this->fileItems($section, $locale, $includeFallback);

        return $databaseItems
            ->keyBy(fn (array $item): string => $this->itemKey($item['publication_type'], $item['locale'], $item['slug']))
            ->union(
                $fileItems->keyBy(fn (array $item): string => $this->itemKey($item['publication_type'], $item['locale'], $item['slug'])),
            )
            ->values();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    protected function mergedAllTypes(string $locale, bool $includeFallback): Collection
    {
        return $this->mergedItems('writing', $locale, $includeFallback)
            ->merge($this->mergedItems('case-studies', $locale, $includeFallback))
            ->values();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    protected function databaseItemsForSection(string $section, string $locale, bool $includeFallback): Collection
    {
        if (! Schema::hasTable('publications')) {
            return collect();
        }

        $types = $section === 'writing'
            ? ['note', 'journal']
            : ['case_study'];

        $locales = $includeFallback && $locale !== 'en'
            ? [$locale, 'en']
            : [$locale];

        $records = Publication::query()
            ->whereIn('type', $types)
            ->whereIn('locale', $locales)
            ->get()
            ->map(fn (Publication $record): array => $this->shapeDatabaseRecord($record));

        return collect($records->all())
            ->sortBy(fn (array $item): int => $item['locale'] === $locale ? 0 : 1)
            ->unique(fn (array $item): string => $this->itemKey($item['publication_type'], $item['locale'], $item['slug']));
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    protected function fileItems(string $section, string $locale, bool $includeFallback): Collection
    {
        $directories = $this->resolveDirectories($section, $locale, $includeFallback);

        return collect($directories)
            ->flatMap(fn (string $directory) => collect(File::files($directory))
                ->filter(fn ($file) => $file->getExtension() === 'md')
                ->map(fn ($file) => $this->parseFile(
                    $section,
                    $file->getPathname(),
                    $this->inferLocale($section, $file->getPathname()),
                )))
            ->sortBy(fn (array $item): int => $item['locale'] === $locale ? 0 : 1)
            ->unique(fn (array $item): string => $this->itemKey($item['publication_type'], $item['locale'], $item['slug']))
            ->values();
    }

    /**
     * @return array<string, mixed>
     */
    protected function parseFile(string $section, string $path, string $locale): array
    {
        $document = YamlFrontMatter::parseFile($path);
        $matter = $document->matter();
        $canonicalUrl = $this->resolveConfiguredUrlPlaceholders(
            (string) ($matter['canonical_url'] ?? $matter['canonical'] ?? ''),
        );
        $openGraphImage = (string) ($matter['open_graph_image'] ?? $matter['ogImage'] ?? ($matter['featured_image'] ?? ''));
        $schema = (string) ($matter['schema'] ?? '');

        foreach (['title', 'slug', 'summary', 'status', 'published_at', 'updated_at', 'tags', 'seo_title', 'seo_description'] as $field) {
            if (! array_key_exists($field, $matter)) {
                throw new RuntimeException("Missing required frontmatter field [{$field}] in [{$path}].");
            }
        }

        if ($section === 'case-studies') {
            foreach (['client', 'role', 'stack', 'outcomes'] as $field) {
                if (! array_key_exists($field, $matter)) {
                    throw new RuntimeException("Missing case study field [{$field}] in [{$path}].");
                }
            }
        }

        $status = (string) $matter['status'];

        if (! in_array($status, ['draft', 'published'], true)) {
            throw new RuntimeException("Invalid status [{$status}] in [{$path}]. Expected draft or published.");
        }

        $html = (string) Str::markdown($document->body(), [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        $publicationType = $this->normalizePublicationType(
            $section,
            (string) ($matter['publication_type'] ?? ''),
            $matter['tags'] ?? [],
        );

        $item = [
            'section' => $section,
            'locale' => $locale,
            'title' => (string) $matter['title'],
            'slug' => (string) $matter['slug'],
            'summary' => (string) $matter['summary'],
            'status' => $status,
            'published_at' => $this->parseDate($matter['published_at'])->toDateString(),
            'updated_at' => $this->parseDate($matter['updated_at'])->toDateString(),
            'tags' => $this->normalizeList($matter['tags']),
            'seo_title' => (string) $matter['seo_title'],
            'seo_description' => (string) $matter['seo_description'],
            'robots' => (string) ($matter['robots'] ?? 'index,follow'),
            'client' => (string) ($matter['client'] ?? ''),
            'role' => (string) ($matter['role'] ?? ''),
            'stack' => $this->normalizeList($matter['stack'] ?? []),
            'outcomes' => $this->normalizeList($matter['outcomes'] ?? []),
            'reading_time' => max(1, (int) ceil(str_word_count(strip_tags($html)) / 220)),
            'body_markdown' => trim($document->body()),
            'body_html' => $html,
            'excerpt' => Str::of(strip_tags($html))->squish()->limit(180)->toString(),
            'url' => $this->publicUrl($publicationType, (string) $matter['slug'], $locale),
            'category' => (string) ($matter['category'] ?? ($section === 'writing' ? 'journal' : 'work')),
            'publication_type' => $publicationType,
            'accent_tone' => (string) ($matter['accent_tone'] ?? ''),
            'featured_image' => (string) ($matter['featured_image'] ?? ''),
            'featured_image_alt' => (string) ($matter['featured_image_alt'] ?? ''),
            'open_graph_image' => $openGraphImage,
            'featured_video' => (string) ($matter['featured_video'] ?? ''),
            'canonical_url' => $canonicalUrl,
            'schema' => $schema,
            'source_path' => $path,
            'source_driver' => 'file',
            'metadata' => [
                'client' => (string) ($matter['client'] ?? ''),
                'role' => (string) ($matter['role'] ?? ''),
                'stack' => $this->normalizeList($matter['stack'] ?? []),
                'outcomes' => $this->normalizeList($matter['outcomes'] ?? []),
                'schema' => $schema,
            ],
        ];

        $image = ContentVisual::image($item);
        $item['image'] = $image;
        $item['image_url'] = $image['url'];
        $item['image_alt'] = $image['alt'];

        return $item;
    }

    /**
     * @return array<string, mixed>
     */
    protected function shapeDatabaseRecord(Publication $record): array
    {
        $bodyMarkdown = $this->bodyMarkdownForRecord($record);
        $html = (string) Str::markdown($bodyMarkdown, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        $metadata = $record->metadata ?? [];
        $item = [
            'section' => $record->type === 'case_study' ? 'case-studies' : 'writing',
            'locale' => $record->locale,
            'title' => $record->title,
            'slug' => $record->slug,
            'summary' => $record->summary,
            'status' => $record->status,
            'published_at' => $record->published_at?->toDateString() ?? '',
            'updated_at' => $record->updated_at_publication?->toDateString() ?? '',
            'tags' => $record->tags ?? [],
            'seo_title' => $record->seo_title,
            'seo_description' => $record->seo_description,
            'robots' => $record->robots,
            'canonical_url' => $this->resolveConfiguredUrlPlaceholders((string) ($record->canonical_url ?? '')),
            'client' => (string) ($metadata['client'] ?? ''),
            'role' => (string) ($metadata['role'] ?? ''),
            'stack' => $this->normalizeList($metadata['stack'] ?? []),
            'outcomes' => $this->normalizeList($metadata['outcomes'] ?? []),
            'reading_time' => max(1, (int) ceil(str_word_count(strip_tags($html)) / 220)),
            'body_markdown' => $bodyMarkdown,
            'body_html' => $html,
            'excerpt' => Str::of(strip_tags($html))->squish()->limit(180)->toString(),
            'url' => $this->publicUrl($record->type, $record->slug, $record->locale),
            'category' => (string) ($record->category ?? ''),
            'publication_type' => $record->type,
            'accent_tone' => (string) ($record->accent_tone ?? ''),
            'featured_image' => (string) ($record->featured_image ?? ''),
            'featured_image_alt' => (string) ($record->featured_image_alt ?? ''),
            'open_graph_image' => (string) ($record->open_graph_image ?? $record->featured_image ?? ''),
            'featured_video' => (string) ($record->featured_video ?? ''),
            'schema' => (string) ($metadata['schema'] ?? ''),
            'source_path' => $record->source_path,
            'source_driver' => $record->source_driver ?: 'hybrid',
            'metadata' => $metadata,
        ];

        $image = ContentVisual::image($item);
        $item['image'] = $image;
        $item['image_url'] = $image['url'];
        $item['image_alt'] = $image['alt'];

        return $item;
    }

    protected function bodyMarkdownForRecord(Publication $record): string
    {
        if (is_string($record->source_path) && $record->source_path !== '' && File::exists($record->source_path)) {
            return $this->markdownFiles->read($record->source_path)['body'];
        }

        return trim((string) $record->body_markdown);
    }

    protected function publicUrl(string $publicationType, string $slug, string $locale): string
    {
        return PublicLocale::localizedPath(
            $publicationType === 'case_study'
                ? "/case-studies/{$slug}"
                : "/journal/{$slug}",
            $locale,
        );
    }

    protected function normalizePublicationType(string $section, string $frontmatterValue, mixed $tags): string
    {
        if ($section === 'case-studies') {
            return 'case_study';
        }

        if (in_array($frontmatterValue, ['note', 'journal'], true)) {
            return $frontmatterValue;
        }

        $normalizedTags = $this->normalizeList($tags);

        return in_array('notes-dev', $normalizedTags, true) ? 'note' : 'journal';
    }

    protected function itemKey(string $type, string $locale, string $slug): string
    {
        return "{$type}:{$locale}:{$slug}";
    }

    /**
     * @return array<int, string>
     */
    protected function resolveDirectories(string $section, string $locale, bool $includeFallback = true): array
    {
        $paths = [resource_path("content/{$section}/{$locale}")];

        if ($locale === 'en' || $includeFallback) {
            $paths[] = resource_path("content/{$section}/en");
            $paths[] = resource_path("content/{$section}");
        }

        return collect($paths)
            ->filter(fn (string $path) => File::isDirectory($path))
            ->unique()
            ->values()
            ->all();
    }

    protected function inferLocale(string $section, string $path): string
    {
        $normalizedPath = str_replace('\\', '/', $path);
        $basePath = str_replace('\\', '/', resource_path("content/{$section}/"));
        $relativePath = Str::after($normalizedPath, $basePath);
        $firstSegment = explode('/', $relativePath)[0] ?? '';

        if ($firstSegment !== '' && ! str_contains($firstSegment, '.')) {
            return $firstSegment;
        }

        return 'en';
    }

    /**
     * @return array<int, string>
     */
    protected function normalizeList(mixed $value): array
    {
        if (is_string($value)) {
            return [$value];
        }

        if (! is_array($value)) {
            return [];
        }

        return array_values(array_map(
            static fn (mixed $item): string => (string) $item,
            $value,
        ));
    }

    protected function parseDate(mixed $value): CarbonImmutable
    {
        if (is_int($value) || (is_string($value) && ctype_digit($value))) {
            return CarbonImmutable::createFromTimestampUTC((int) $value);
        }

        return CarbonImmutable::parse((string) $value);
    }

    protected function resolvePublicationSourcePath(string $type, string $locale, string $slug): string
    {
        $section = $type === 'case_study' ? 'case-studies' : 'writing';

        return resource_path("content/{$section}/{$locale}/{$slug}.md");
    }

    protected function normalizeSourcePath(?string $candidate, string $type, string $locale, string $slug): string
    {
        if (! is_string($candidate) || trim($candidate) === '') {
            return $this->resolvePublicationSourcePath($type, $locale, $slug);
        }

        $normalized = str_replace('\\', '/', trim($candidate));

        if (Str::startsWith($normalized, str_replace('\\', '/', resource_path()))) {
            return $candidate;
        }

        if (Str::startsWith($normalized, 'resources/content/')) {
            return base_path($normalized);
        }

        return $this->resolvePublicationSourcePath($type, $locale, $slug);
    }

    protected function resolveConfiguredUrlPlaceholders(string $value): string
    {
        $normalized = trim($value);

        if ($normalized === '') {
            return '';
        }

        $siteUrl = rtrim((string) config('site.url'), '/');

        foreach ($this->siteUrlPlaceholders() as $placeholder) {
            if ($placeholder !== '' && str_contains($normalized, $placeholder)) {
                $normalized = str_replace($placeholder, $siteUrl, $normalized);
            }
        }

        return $normalized;
    }

    /**
     * @return array<int, string>
     */
    protected function siteUrlPlaceholders(): array
    {
        return array_values(array_filter(array_unique([
            (string) config('site.url_placeholder', '{{site_url}}'),
            '{{site_url}}',
            '{site_url}',
            '__SITE_URL__',
        ])));
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $existingMatter
     * @return array<string, mixed>
     */
    protected function publicationFrontmatter(array $payload, array $existingMatter): array
    {
        $preserved = collect($existingMatter)
            ->except([
                'title',
                'slug',
                'summary',
                'status',
                'published_at',
                'updated_at',
                'tags',
                'seo_title',
                'seo_description',
                'robots',
                'canonical_url',
                'featured_image',
                'featured_image_alt',
                'open_graph_image',
                'featured_video',
                'category',
                'publication_type',
                'accent_tone',
                'client',
                'role',
                'stack',
                'outcomes',
                'locale',
            ])
            ->all();

        return array_replace($preserved, [
            'title' => $payload['title'],
            'publication_type' => $payload['publication_type'],
            'locale' => $payload['locale'],
            'updated_at' => $payload['updated_at'] ?: CarbonImmutable::now()->toDateString(),
            'managed_by' => 'sidewalk-admin',
        ]);
    }

    protected function findRecord(string $type, string $locale, string $slug): ?Publication
    {
        if (! Schema::hasTable('publications')) {
            return null;
        }

        return Publication::query()
            ->where('type', $type)
            ->where('locale', $locale)
            ->where('slug', $slug)
            ->first();
    }
}
