<?php

namespace Tests\Feature;

use App\Models\ExperienceEntry;
use App\Services\ContentImportService;
use App\Services\ExperienceEntryRepository;
use App\Services\PageContentRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The experience record, filed as rows.
 *
 * The chronology was three arrays inside a page payload, ordered by the
 * position of an item in an array, with each period written as prose inside
 * the same string as the role. This is the move to rows, and the first test is
 * the one that makes the move safe: the rows have to reproduce the payload
 * exactly, or the reader sees the refactor.
 */
class ExperienceEntriesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(ContentImportService::class)->importAll();
    }

    /**
     * The invariant that lets this ship: nothing a reader could see moved.
     *
     * Compared as encoded JSON rather than with `assertEquals`, because the
     * order of keys inside a section is part of what the front end receives
     * and a loose comparison would let it drift.
     */
    public function test_the_rows_reproduce_the_page_payload_exactly(): void
    {
        $pages = app(PageContentRepository::class);
        $entries = app(ExperienceEntryRepository::class);

        foreach (['en', 'fr'] as $locale) {
            $this->app->setLocale($locale);

            $payload = $pages->seededPage('experience', $locale)['payload'];
            $rebuilt = $entries->sectionsFor($locale);

            foreach (ExperienceEntry::KINDS as $kind) {
                $key = $entries->payloadKeyFor($kind);

                $this->assertSame(
                    json_encode(array_values($payload[$key] ?? []), JSON_UNESCAPED_UNICODE),
                    json_encode(array_values($rebuilt[$kind] ?? []), JSON_UNESCAPED_UNICODE),
                    "The [{$kind}] rows no longer reproduce [{$key}] in [{$locale}].",
                );
            }
        }
    }

    /**
     * A job is one thing held in two languages, not two things.
     *
     * The organisation name cannot be the key — `Jewely E-commerce` and
     * `Jewely Ecommerce` are the same employer spelled twice — which is the
     * same reason publications carry a `translation_key`.
     */
    public function test_each_entry_is_paired_with_its_other_language_self(): void
    {
        $keys = ExperienceEntry::query()
            ->get()
            ->groupBy('translation_key')
            ->map(fn ($rows) => $rows->pluck('locale')->sort()->values()->all());

        $this->assertNotEmpty($keys);

        foreach ($keys as $key => $locales) {
            $this->assertSame(
                ['en', 'fr'],
                $locales,
                "The entry [{$key}] does not exist in both languages.",
            );
        }
    }

    /**
     * Newest first, and the undated last.
     *
     * `Avant 2023` means "before everything dated here". A null start date
     * that floated to the top would put the vaguest entry above the current
     * job, which is the one failure mode a CV cannot have.
     */
    public function test_dated_entries_lead_the_chronology_and_undated_ones_close_it(): void
    {
        $seeded = ExperienceEntry::query()
            ->where('locale', 'fr')
            ->where('kind', 'professional')
            ->first();
        $this->assertNotNull($seeded);

        ExperienceEntry::query()->where('locale', 'fr')->update(['started_on' => null]);

        $recent = ExperienceEntry::query()->create([
            'translation_key' => 'a-recent-post',
            'locale' => 'fr',
            'kind' => 'professional',
            'organisation' => 'Recent',
            'role' => 'Lead',
            'started_on' => '2025-01-01',
            'summary' => 'A dated entry.',
            'paragraphs' => [],
            'detail_groups' => [],
        ]);

        $older = ExperienceEntry::query()->create([
            'translation_key' => 'an-older-post',
            'locale' => 'fr',
            'kind' => 'professional',
            'organisation' => 'Older',
            'role' => 'Developer',
            'started_on' => '2018-01-01',
            'summary' => 'An older dated entry.',
            'paragraphs' => [],
            'detail_groups' => [],
        ]);

        $order = ExperienceEntry::query()
            ->where('locale', 'fr')
            ->where('kind', 'professional')
            ->inChronologicalOrder()
            ->pluck('id')
            ->all();

        $this->assertSame($recent->id, $order[0], 'The most recent dated entry does not lead.');
        $this->assertSame($older->id, $order[1], 'The older dated entry is not second.');
        $this->assertContains($seeded->id, array_slice($order, 2), 'An undated entry outranked a dated one.');
    }

    /**
     * The point of the whole move: the reader sees the rows.
     *
     * Both halves matter. An edit to a row has to reach `/fr/experience`, and
     * the order has to come from the dates rather than from the order someone
     * typed the entries in — so the dates are set deliberately against the
     * seeded position here, and the page is expected to disagree with it.
     */
    public function test_the_public_chronology_follows_the_rows_and_their_dates(): void
    {
        $professional = ExperienceEntry::query()
            ->where('locale', 'fr')
            ->where('kind', 'professional')
            ->orderBy('position')
            ->get();

        $this->assertGreaterThanOrEqual(2, $professional->count());

        $last = $professional->last();
        $first = $professional->first();

        // The entry typed last is given the most recent start date.
        $last->update([
            'organisation' => 'Maison la plus récente',
            'date_label' => null,
            'started_on' => '2030-01-01',
            'ended_on' => null,
        ]);
        $first->update(['started_on' => '2001-01-01', 'date_label' => null, 'ended_on' => '2002-01-01']);

        $response = $this->get('/fr/experience')->assertOk();

        $sections = $response->viewData('page')['props']['professionalSections'];

        $this->assertSame(
            'Maison la plus récente',
            $sections[0]['title'],
            'The chronology is still following the order the entries were typed in.',
        );
        $this->assertSame(
            $last->role.ExperienceEntryRepository::EYEBROW_SEPARATOR.'Depuis 2030',
            $sections[0]['eyebrow'],
            'The eyebrow is not being composed from the row.',
        );
        $range = fn (array $section): string => explode(
            ExperienceEntryRepository::EYEBROW_SEPARATOR,
            (string) $section['eyebrow'],
        )[1] ?? '';

        $this->assertSame(
            '2001–2002',
            $range($sections[1]),
            'The older dated entry did not follow the current one.',
        );

        /**
         * The third entry still has no start date, so it closes the list
         * behind both dated ones and keeps showing the label it was seeded
         * with. That ordering is the rule, not an accident of this fixture:
         * an undated row means "before everything dated here".
         */
        $this->assertSame(
            '2024-2026',
            $range(end($sections)),
            'An undated entry did not close the chronology.',
        );
    }

    /**
     * What a row says about its own dates once nobody is telling it.
     *
     * The label is the escape hatch for imprecise history and it wins while it
     * is set. Clearing it is what hands the range to the dates — and an entry
     * with no end is the one being lived, which is the whole reason to store
     * them: the page stops needing an edit every January.
     */
    public function test_a_row_computes_its_own_range_once_the_label_is_cleared(): void
    {
        $entries = app(ExperienceEntryRepository::class);
        $this->app->setLocale('fr');

        $entry = new ExperienceEntry([
            'date_label' => 'Avant 2023',
            'started_on' => '2019-04-01',
            'ended_on' => '2022-08-01',
        ]);

        $this->assertSame('Avant 2023', $entries->dateRangeFor($entry));

        $entry->date_label = null;
        $this->assertSame('2019–2022', $entries->dateRangeFor($entry));

        $entry->ended_on = null;
        $this->assertSame('Depuis 2019', $entries->dateRangeFor($entry));

        $this->app->setLocale('en');
        $this->assertSame('Since 2019', $entries->dateRangeFor($entry));

        $entry->ended_on = '2019-11-01';
        $this->assertSame('2019', $entries->dateRangeFor($entry));
    }
}
