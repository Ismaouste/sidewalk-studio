<?php

namespace Tests\Feature;

use App\Content\ContentSource;
use App\Content\Schema\PageSchemas;
use App\Models\Page;
use App\Models\Publication;
use App\Services\ContentImportService;
use App\Services\PageContentRepository;
use App\Support\PublicLocale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * Seeding loses nothing.
 *
 * This is the test that de-risks the precedence reversal, and it has to be
 * more than "the seeder ran without throwing". The claim being made is that
 * `migrate:fresh --seed` produces a database whose *rendered output* matches
 * what the Markdown rendered — so the test renders the real routes twice in
 * one process, once from each source, and compares the props the page
 * component actually receives.
 *
 * Comparing props rather than HTML is deliberate. HTML carries a CSRF token,
 * a randomly chosen loader quote and asset hashes, none of which say anything
 * about content fidelity; the Inertia payload is exactly the content and
 * nothing else.
 */
class ContentSeedFidelityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Routes worth comparing: every public page, plus one publication index
     * and one detail page per section. The detail pages are where a
     * publication's body and metadata are visible, and the indexes are where
     * ordering and filtering would show a difference.
     *
     * @return array<int, string>
     */
    protected function routesUnderTest(): array
    {
        return [
            '/en',
            '/en/projects',
            '/en/local',
            '/en/contact',
            '/en/colophon',
            '/en/data-processing',
            '/en/sparkle',
            '/en/journal',
            '/en/journal/content-systems-routing-and-metadata',
            '/en/case-studies',
            '/en/case-studies/consent-orchestration-before-analytics',
            '/fr',
            '/fr/projects',
            '/fr/local',
            '/fr/contact',
            '/fr/journal',
            '/fr/journal/quand-un-deploiement-reussi-ne-lest-pas',
        ];
    }

    public function test_rendering_from_the_seeded_database_matches_rendering_from_markdown(): void
    {
        $fromFiles = [];

        config(['site.content_source' => ContentSource::Files->value]);

        foreach ($this->routesUnderTest() as $route) {
            $fromFiles[$route] = $this->propsFor($route);
        }

        app(ContentImportService::class)->importAll();

        config(['site.content_source' => ContentSource::Database->value]);

        foreach ($this->routesUnderTest() as $route) {
            $this->assertSame(
                $fromFiles[$route],
                $this->propsFor($route),
                "Seeding changed what [{$route}] renders.",
            );
        }
    }

    /**
     * Seeding twice is seeding once. A seeder that is not idempotent turns
     * every deploy that runs it into a duplicate-content incident.
     */
    public function test_seeding_twice_changes_nothing(): void
    {
        $importer = app(ContentImportService::class);

        $importer->importAll();

        $after = [
            'publications' => Publication::query()->count(),
            'pages' => Page::query()->count(),
        ];

        $importer->importAll();

        $this->assertSame($after, [
            'publications' => Publication::query()->count(),
            'pages' => Page::query()->count(),
        ]);
    }

    /**
     * The seeded publications carry their translation pairing, or the admin
     * cannot offer "the French version of this" once rows are authoritative.
     */
    public function test_seeded_publications_keep_their_translation_pairing(): void
    {
        app(ContentImportService::class)->importAll();

        $unpaired = Publication::query()
            ->select('type', 'translation_key')
            ->get()
            ->groupBy(fn ($record): string => $record->type.':'.$record->translation_key)
            ->reject(fn ($group): bool => $group->count() === 2)
            ->keys()
            ->all();

        $this->assertSame([], $unpaired);
    }

    /**
     * The revert path an operator needs once the database is authoritative:
     * the Markdown stays on disk, unwritten by the admin, and still readable.
     */
    public function test_the_seeded_page_stays_readable_after_the_database_wins(): void
    {
        app(ContentImportService::class)->importAll();

        config(['site.content_source' => ContentSource::Database->value]);

        $repository = app(PageContentRepository::class);

        foreach (PageSchemas::KEYS as $key) {
            foreach (PublicLocale::supported() as $locale) {
                $seeded = $repository->seededPage($key, $locale);

                $this->assertNotNull($seeded, "No seed readable for {$locale}/{$key}.");
                $this->assertSame('file', $seeded['source_driver']);
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function propsFor(string $route): array
    {
        $props = [];

        $this->get($route)
            ->assertOk()
            ->assertInertia(function (Assert $page) use (&$props): void {
                $props = $page->toArray()['props'];
            });

        /**
         * `site` carries the request's own state — the active navigation
         * entry, the language switcher, a randomly chosen colophon quote —
         * none of which is content. What is left is what the page is made of.
         */
        unset($props['site'], $props['auth'], $props['flash'], $props['consent'], $props['name']);

        return $this->normalise($props);
    }

    /**
     * Two things differ between the sources by design, and neither is content.
     *
     * `source_driver` is provenance: it says whether this item came from a
     * file or a row, which is the one thing that *must* differ once the
     * database wins. And the two shapes assemble their keys in a different
     * order — the file builder emits `canonical_url` after `featured_video`,
     * the row builder before `client` — which `assertSame` counts as a
     * difference on associative arrays and no reader ever would.
     *
     * Everything else is compared exactly, including `source_path`, which
     * should agree because a seeded row records the file it came from.
     *
     * @param  array<mixed>  $value
     * @return array<mixed>
     */
    protected function normalise(array $value): array
    {
        unset($value['source_driver']);

        foreach ($value as $key => $item) {
            if (is_array($item)) {
                $value[$key] = $this->normalise($item);
            }
        }

        if (! array_is_list($value)) {
            ksort($value);
        }

        return $value;
    }
}
