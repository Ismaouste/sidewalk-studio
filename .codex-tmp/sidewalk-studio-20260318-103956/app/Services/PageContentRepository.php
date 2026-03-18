<?php

namespace App\Services;

use App\Models\Page;
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
        $item = $this->merged($page, $locale ?? app()->getLocale());

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
        $page = $this->merged($pageKey, $locale);

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
            ->map(fn (string $page): ?array => $this->merged($page, $locale))
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
     * @return array<string, mixed>|null
     */
    protected function merged(string $page, string $locale): ?array
    {
        $filePayload = $this->loadFilePage($page, $locale);
        $databasePayload = $this->loadDatabasePage($page, $locale);

        if ($databasePayload === null) {
            return $filePayload;
        }

        if ($filePayload === null) {
            return $databasePayload;
        }

        $payload = array_replace_recursive($filePayload, $databasePayload);
        $payload['payload'] = array_replace_recursive($filePayload['payload'] ?? [], $databasePayload['payload'] ?? []);

        return $payload;
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

        foreach (['seo_title', 'seo_description'] as $field) {
            if (! array_key_exists($field, $matter)) {
                throw new RuntimeException("Missing required frontmatter field [{$field}] in [{$path}].");
            }
        }

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
