<?php

namespace Tests\Feature;

use App\Services\ContentRepository;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Tests\TestCase;

class ContentRepositoryTest extends TestCase
{
    public function test_repository_returns_published_writing_entries(): void
    {
        $items = app(ContentRepository::class)->published('writing');

        $this->assertCount(2, $items);
        $this->assertContains('content-systems-routing-and-metadata', $items->pluck('slug')->all());
        $this->assertTrue($items->every(fn (array $item) => $item['status'] === 'published'));
    }

    public function test_repository_returns_case_study_details(): void
    {
        $item = app(ContentRepository::class)->findPublished('case-studies', 'repo-bootstrap-foundation');

        $this->assertSame('Sidewalk Studio', $item['client']);
        $this->assertSame('en', $item['locale']);
        $this->assertContains('Laravel 12', $item['stack']);
        $this->assertStringContainsString('The first iteration of Sidewalk Studio started from a mismatch', $item['body_html']);
    }

    public function test_repository_prefers_locale_specific_collection_entries_and_falls_back_to_english(): void
    {
        $directory = resource_path('content/writing/fr');
        $path = "{$directory}/editorial-locale-priority-test.md";
        $fallbackPath = resource_path('content/writing/en/editorial-english-fallback-test.md');

        File::ensureDirectoryExists($directory);
        File::put($path, <<<'MD'
---
title: Editorial Locale Priority Test
slug: editorial-locale-priority-test
summary: A locale-specific entry should replace the English entry when both share the same slug.
status: published
published_at: 2026-03-08
updated_at: 2026-03-08
tags:
    - ssr
    - inertia
    - strategy
seo_title: Editorial Locale Priority Test
seo_description: This entry proves that locale-specific content overrides the English fallback for the same slug.
---

This locale-specific version should be served before the English fallback.
MD);
        File::put($fallbackPath, <<<'MD'
---
title: Editorial English Fallback Test
slug: editorial-english-fallback-test
summary: An English-only entry should stay available when no French translation exists.
status: published
published_at: 2026-03-08
updated_at: 2026-03-08
tags:
    - content
    - fallback
seo_title: Editorial English Fallback Test
seo_description: This entry proves English fallback remains available when French content is missing.
---

This English-only entry should still be present in a French collection response.
MD);

        try {
            $items = app(ContentRepository::class)->published('writing', 'fr');

            $localized = $items->firstWhere('slug', 'editorial-locale-priority-test');
            $fallback = $items->firstWhere('slug', 'editorial-english-fallback-test');

            $this->assertSame('fr', $localized['locale']);
            $this->assertStringContainsString('This locale-specific version should be served', $localized['body_html']);
            $this->assertSame('en', $fallback['locale']);
        } finally {
            File::delete($path);
            File::delete($fallbackPath);
        }
    }

    public function test_repository_keeps_legacy_root_files_as_a_last_fallback_source(): void
    {
        $path = resource_path('content/writing/legacy-root-fallback.md');

        File::put($path, <<<'MD'
---
title: Legacy Root Fallback
slug: legacy-root-fallback
summary: A temporary root-level file should still be available during the locale migration.
status: published
published_at: 2026-03-07
updated_at: 2026-03-07
tags:
    - content
seo_title: Legacy Root Fallback
seo_description: This entry proves the repository still reads root-level collection files as a last fallback.
---

Legacy files remain readable while the collection folders migrate to locale-specific paths.
MD);

        try {
            $item = app(ContentRepository::class)
                ->published('writing', 'fr')
                ->firstWhere('slug', 'legacy-root-fallback');

            $this->assertNotNull($item);
            $this->assertSame('en', $item['locale']);
        } finally {
            File::delete($path);
        }
    }

    public function test_repository_raises_a_test_visible_failure_for_invalid_frontmatter(): void
    {
        $path = resource_path('content/writing/invalid-frontmatter.md');

        File::put($path, <<<'MD'
---
title: Invalid frontmatter entry
slug: invalid-frontmatter-entry
summary: This file intentionally misses updated_at.
status: published
published_at: 2026-03-07
tags:
    - test
seo_title: Invalid frontmatter entry
seo_description: This file is used to prove invalid frontmatter fails fast.
---

This entry should never be parsed successfully.
MD);

        try {
            app(ContentRepository::class)->all('writing');
            $this->fail('Expected invalid frontmatter to raise a runtime exception.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString(
                'Missing required frontmatter field [updated_at]',
                $exception->getMessage(),
            );
        } finally {
            File::delete($path);
        }
    }
}
