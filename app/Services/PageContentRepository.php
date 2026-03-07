<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use RuntimeException;
use Spatie\YamlFrontMatter\YamlFrontMatter;

class PageContentRepository
{
    /**
     * @return array<string, mixed>
     */
    public function get(string $page, ?string $locale = null): array
    {
        $path = $this->resolvePath($page, $locale ?? app()->getLocale());

        if (! $path) {
            throw new RuntimeException("Missing page content file for [{$page}].");
        }

        $document = YamlFrontMatter::parseFile($path);
        $matter = $document->matter();

        foreach (['seo_title', 'seo_description'] as $field) {
            if (! array_key_exists($field, $matter)) {
                throw new RuntimeException("Missing required frontmatter field [{$field}] in [{$path}].");
            }
        }

        return $matter;
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
