<?php

namespace Tests\Feature;

use App\Content\Schema\PublicationSchemas;
use App\Models\Publication;
use App\Services\ContentRepository;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Spatie\YamlFrontMatter\YamlFrontMatter;
use Tests\TestCase;

class ContentRepositoryTest extends TestCase
{
    public function test_repository_returns_published_writing_entries(): void
    {
        $items = app(ContentRepository::class)->published('writing');

        $this->assertCount(9, $items);
        $this->assertContains('content-systems-routing-and-metadata', $items->pluck('slug')->all());
        $this->assertContains('opensurvey-nonprofit-health-data', $items->pluck('slug')->all());
        $this->assertContains('quand-un-deploiement-reussi-ne-lest-pas', $items->pluck('slug')->all());
        $this->assertContains('les-interdits-comme-specification', $items->pluck('slug')->all());
        $this->assertContains('schema-org-rich-results-and-product-images', $items->pluck('slug')->all());
        $this->assertContains('technical-seo-sitemaps-and-structured-data-for-commerce', $items->pluck('slug')->all());
        $this->assertContains('ytmusic-liked-sorter', $items->pluck('slug')->all());
        $this->assertTrue($items->every(fn (array $item) => $item['status'] === 'published'));
        $this->assertContains('journal', $items->pluck('category')->all());
        $this->assertContains('note', $items->pluck('category')->all());
        $this->assertContains('note', $items->pluck('publication_type')->all());
        $this->assertStringContainsString('/content-visuals/writing/', $items->first()['image_url']);
        $contentSystems = $items->firstWhere('slug', 'content-systems-routing-and-metadata');
        $this->assertSame(
            '/images/og/content-systems-routing-and-metadata.jpg',
            $contentSystems['open_graph_image'],
        );
        $this->assertSame(
            rtrim((string) config('site.url'), '/').'/en/journal/content-systems-routing-and-metadata',
            $contentSystems['canonical_url'],
        );
    }

    public function test_repository_returns_new_published_french_writing_entries(): void
    {
        $items = app(ContentRepository::class)->published('writing', 'fr', false);

        $this->assertContains('quand-un-deploiement-reussi-ne-lest-pas', $items->pluck('slug')->all());
        $this->assertContains('les-interdits-comme-specification', $items->pluck('slug')->all());
        $this->assertTrue($items->every(fn (array $item) => $item['locale'] === 'fr'));
    }

    public function test_repository_adds_locale_hint_to_localized_placeholder_urls(): void
    {
        $item = app(ContentRepository::class)->findPublished('writing', 'opensurvey-associatif-donnees-sante', 'fr');

        $this->assertSame('fr', $item['locale']);
        $this->assertStringContainsString('/content-visuals/writing/opensurvey-associatif-donnees-sante.svg', $item['image_url']);
        $this->assertStringNotContainsString('lang=', $item['image_url']);
        $this->assertStringNotContainsString('path=', $item['image_url']);
    }

    public function test_repository_returns_case_study_details(): void
    {
        $item = app(ContentRepository::class)->findPublished('case-studies', 'pipeline-deploiement-ecommerce');

        $this->assertSame('Jewely / Flippad', $item['client']);
        $this->assertSame('en', $item['locale']);
        $this->assertContains('Docker Swarm', $item['stack']);
        $this->assertSame('work', $item['category']);
        $this->assertSame('case_study', $item['publication_type']);
        $this->assertStringContainsString('/content-visuals/case-studies/', $item['image_url']);
        $this->assertStringContainsString('The pipeline already existed', $item['body_html']);
    }

    public function test_repository_prefers_markdown_over_hybrid_database_records(): void
    {
        // This case needs a seeded row, so it checks for the row as well as
        // the table. A RefreshDatabase case earlier in the run migrates the
        // database fresh without seeding, which leaves the table in place and
        // empty — a state `Schema::hasTable` alone reports as fine.
        $seededRecord = fn () => Publication::query()
            ->where('type', 'case_study')
            ->where('locale', 'fr')
            ->where('slug', 'pipeline-deploiement-ecommerce');

        if (! Schema::hasTable('publications') || $seededRecord()->doesntExist()) {
            $this->artisan('migrate:fresh', ['--seed' => true]);
        }

        $record = $seededRecord()->firstOrFail();

        $metadata = $record->metadata ?? [];
        $metadata['role'] = 'Old database role';

        $record->forceFill([
            'summary' => 'Old database summary that should never leak publicly.',
            'metadata' => $metadata,
        ])->save();

        $item = app(ContentRepository::class)->findPublished('case-studies', 'pipeline-deploiement-ecommerce', 'fr');

        $this->assertSame(
            'Analyse incident et stabilisation du déploiement',
            $item['role'],
        );
        $this->assertSame(
            'Un case study e-commerce sur un Docker Swarm rollback silencieux, un déploiement automatique trompeur et le besoin de rendre l\'état final vérifiable.',
            $item['summary'],
        );
    }

    public function test_repository_feed_can_filter_publications_by_tag_and_category(): void
    {
        $items = app(ContentRepository::class)->feed(['writing', 'case-studies'], [
            'tag' => 'notes-dev',
            'category' => 'journal',
            'limit' => 2,
        ]);

        $this->assertCount(2, $items);
        $this->assertTrue($items->every(fn (array $item): bool => $item['section'] === 'writing'));
        $this->assertTrue($items->every(fn (array $item): bool => in_array('notes-dev', $item['tags'], true)));
    }

    public function test_repository_prefers_locale_specific_case_studies_and_falls_back_to_english(): void
    {
        $directory = resource_path('content/case-studies/fr');
        $path = "{$directory}/case-study-locale-priority-test.md";
        $fallbackPath = resource_path('content/case-studies/en/case-study-english-fallback-test.md');

        File::ensureDirectoryExists($directory);
        File::put($path, <<<'MD'
---
title: Case Study Locale Priority Test
slug: case-study-locale-priority-test
translation_key: case-study-locale-priority-test
category: work
accent_tone: violet
summary: A localized case study should replace the English version when both share the same slug.
status: published
published_at: 2026-03-09
updated_at: 2026-03-09
tags:
    - privacy
    - architecture
seo_title: Case Study Locale Priority Test
seo_description: This case study proves locale-specific case studies override the English fallback for the same slug.
client: Sidewalk Studio
role: Architecture
stack:
    - Laravel 12
outcomes:
    - Locale-specific case studies win
---

This localized case study should be served before the English fallback.
MD);
        File::put($fallbackPath, <<<'MD'
---
title: Case Study English Fallback Test
slug: case-study-english-fallback-test
translation_key: case-study-english-fallback-test
category: work
accent_tone: violet
summary: An English-only case study should stay available when no French translation exists.
status: published
published_at: 2026-03-09
updated_at: 2026-03-09
tags:
    - fallback
seo_title: Case Study English Fallback Test
seo_description: This case study proves English fallback remains available when French case studies are missing.
client: Sidewalk Studio
role: Architecture
stack:
    - Laravel 12
outcomes:
    - English fallback remains visible
---

This English-only case study should still be present in a French collection response.
MD);

        try {
            $items = app(ContentRepository::class)->published('case-studies', 'fr');

            $localized = $items->firstWhere('slug', 'case-study-locale-priority-test');
            $fallback = $items->firstWhere('slug', 'case-study-english-fallback-test');

            $this->assertSame('fr', $localized['locale']);
            $this->assertStringContainsString('This localized case study should be served', $localized['body_html']);
            $this->assertSame('en', $fallback['locale']);
        } finally {
            File::delete($path);
            File::delete($fallbackPath);
        }
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
translation_key: editorial-locale-priority-test
category: journal
accent_tone: violet
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
translation_key: editorial-english-fallback-test
category: journal
accent_tone: violet
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
translation_key: legacy-root-fallback
category: journal
accent_tone: violet
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
translation_key: invalid-frontmatter-entry
category: journal
accent_tone: violet
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
                '[updated_at] is required and missing',
                $exception->getMessage(),
            );
        } finally {
            File::delete($path);
        }
    }

    /**
     * The check the old presence list could not perform, and the reason this
     * feature exists.
     *
     * A colon-space inside an unquoted YAML scalar makes the parser return a
     * one-key mapping instead of a string. The value is still *there*, so a
     * presence list passes it, and the page renders a JSON blob at its
     * readers — which is exactly what /fr/projects did until step 0 of this
     * spec.
     *
     * The trap is only silent inside a *sequence*, which is why the defect
     * survived where it did. Written at mapping level — `summary: a: b` —
     * YAML refuses to parse the document at all and the failure is loud and
     * immediate. Written as a list item, it resolves quietly to a mapping and
     * travels all the way to the reader's screen. So the fixture puts it in a
     * list, where the real one was.
     */
    public function test_repository_rejects_a_value_whose_type_is_wrong_not_only_one_that_is_absent(): void
    {
        $path = resource_path('content/writing/colon-trap.md');

        File::put($path, <<<'MD'
---
title: Colon trap
slug: colon-trap
translation_key: colon-trap
category: journal
accent_tone: violet
summary: A tag below is not a tag.
status: published
published_at: 2026-03-07
updated_at: 2026-03-07
tags:
    - notes-dev
    - here is the trap: an unquoted scalar with a colon-space
seo_title: Colon trap
seo_description: This file proves a wrongly typed value fails as loudly as a missing one.
---

The second tag parses as a mapping, not a string.
MD);

        try {
            app(ContentRepository::class)->all('writing');
            $this->fail('Expected a wrongly typed value to raise a runtime exception.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('[tags.1] should be a line', $exception->getMessage());
            $this->assertStringContainsString('got a mapping', $exception->getMessage());
        } finally {
            File::delete($path);
        }
    }

    /**
     * Every publication in the repository validates against its own
     * declaration. This is the check that runs in CI, and the one that would
     * have made the /fr/projects defect impossible to ship.
     */
    public function test_every_publication_on_disk_validates_against_its_declaration(): void
    {
        $failures = [];

        foreach (PublicationSchemas::SECTIONS as $section) {
            $schema = PublicationSchemas::for($section);

            foreach (glob(resource_path("content/{$section}/*/*.md")) as $file) {
                $violations = $schema->violations(
                    YamlFrontMatter::parseFile($file)->matter(),
                );

                foreach ($violations as $violation) {
                    $failures[] = basename(dirname($file)).'/'.basename($file).': '.$violation;
                }
            }
        }

        $this->assertSame([], $failures);
    }

    /**
     * A translation and its original are two files that say they are the same
     * publication. Nothing linked them before: each locale was a directory,
     * and the directory was the link — which stops being true the moment
     * publications are rows in one table.
     */
    public function test_translation_keys_pair_each_publication_with_its_other_locale(): void
    {
        foreach (PublicationSchemas::SECTIONS as $section) {
            $byKey = [];

            foreach (glob(resource_path("content/{$section}/*/*.md")) as $file) {
                $matter = YamlFrontMatter::parseFile($file)->matter();
                $byKey[$matter['translation_key']][] = basename(dirname($file));
            }

            foreach ($byKey as $key => $locales) {
                sort($locales);

                $this->assertSame(
                    ['en', 'fr'],
                    $locales,
                    "Translation key [{$key}] in [{$section}] does not pair one "
                    .'English file with one French file.',
                );
            }
        }
    }
}
