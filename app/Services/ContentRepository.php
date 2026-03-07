<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;
use Spatie\YamlFrontMatter\YamlFrontMatter;

class ContentRepository
{
    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function all(string $section, ?string $locale = null): Collection
    {
        $directories = $this->resolveDirectories($section, $locale ?? app()->getLocale());

        return collect($directories)
            ->flatMap(fn (string $directory) => collect(File::files($directory))
                ->filter(fn ($file) => $file->getExtension() === 'md')
                ->map(fn ($file) => $this->parseFile(
                    $section,
                    $file->getPathname(),
                    $this->inferLocale($section, $file->getPathname()),
                )))
            ->unique('slug')
            ->sortByDesc(fn (array $item) => $item['published_at'])
            ->values();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function published(string $section, ?string $locale = null): Collection
    {
        return $this->all($section, $locale)
            ->where('status', 'published')
            ->values();
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
     * @return array<string, mixed>
     */
    protected function parseFile(string $section, string $path, string $locale): array
    {
        $document = YamlFrontMatter::parseFile($path);
        $matter = $document->matter();

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

        $publishedAt = $this->parseDate($matter['published_at']);
        $updatedAt = $this->parseDate($matter['updated_at']);

        $url = $section === 'writing'
            ? route('writing.show', $matter['slug'])
            : route('case-studies.show', $matter['slug']);

        return [
            'section' => $section,
            'locale' => $locale,
            'title' => (string) $matter['title'],
            'slug' => (string) $matter['slug'],
            'summary' => (string) $matter['summary'],
            'status' => $status,
            'published_at' => $publishedAt->toDateString(),
            'updated_at' => $updatedAt->toDateString(),
            'tags' => $this->normalizeList($matter['tags']),
            'seo_title' => (string) $matter['seo_title'],
            'seo_description' => (string) $matter['seo_description'],
            'client' => (string) ($matter['client'] ?? ''),
            'role' => (string) ($matter['role'] ?? ''),
            'stack' => $this->normalizeList($matter['stack'] ?? []),
            'outcomes' => $this->normalizeList($matter['outcomes'] ?? []),
            'reading_time' => max(1, (int) ceil(str_word_count(strip_tags($html)) / 220)),
            'body_html' => $html,
            'excerpt' => Str::of(strip_tags($html))->squish()->limit(180)->toString(),
            'url' => $url,
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function resolveDirectories(string $section, string $locale): array
    {
        return collect([
            resource_path("content/{$section}/{$locale}"),
            resource_path("content/{$section}/en"),
            resource_path("content/{$section}"),
        ])
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
}
