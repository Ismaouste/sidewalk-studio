<?php

namespace Tests\Feature;

use App\Models\ExperienceEntry;
use App\Models\User;
use App\Services\ContentImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Entering a position, from the operator's side.
 *
 * The chronology is the one editorial surface where a half-finished save is
 * visible as a factual error rather than as a gap: a career that reads
 * differently depending on which flag the reader clicked. So the pairing is
 * asserted on every write, not only on the seed.
 */
class AdminExperienceEntriesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(ContentImportService::class)->importAll();
    }

    /**
     * @return array<string, mixed>
     */
    protected function position(array $overrides = []): array
    {
        return [
            'locale' => 'fr',
            'kind' => 'professional',
            'organisation' => 'Atelier Cartographie',
            'role' => 'Tech lead',
            'started_on' => '2026-02-01',
            'ended_on' => null,
            'date_label' => null,
            'summary' => 'Reprise d’une plateforme de cartographie.',
            'paragraphs' => ['Un premier paragraphe.'],
            'detail_groups' => [
                ['title' => 'Stack', 'pills' => ['Laravel'], 'items' => ['Un point.']],
            ],
            ...$overrides,
        ];
    }

    public function test_adding_a_position_files_it_in_both_languages(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/admin/experience', $this->position())
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/experience?locale=fr');

        $filed = ExperienceEntry::query()
            ->where('organisation', 'Atelier Cartographie')
            ->get();

        $this->assertCount(2, $filed, 'A position was filed in one language only.');
        $this->assertSame(
            ['en', 'fr'],
            $filed->pluck('locale')->sort()->values()->all(),
        );
        $this->assertCount(
            1,
            $filed->pluck('translation_key')->unique(),
            'The two languages of one position were given different keys.',
        );
    }

    /**
     * The reason the whole record moved to rows: an edit is publishing.
     */
    public function test_an_edited_position_reaches_the_public_chronology(): void
    {
        $entry = ExperienceEntry::query()
            ->where('locale', 'fr')
            ->where('kind', 'professional')
            ->firstOrFail();

        $this->actingAs(User::factory()->create())
            ->put("/admin/experience/{$entry->id}", $this->position([
                'organisation' => 'Renommé depuis l’admin',
                'started_on' => '2040-01-01',
                'date_label' => null,
            ]))
            ->assertSessionHasNoErrors();

        $sections = $this->get('/fr/projects')
            ->assertOk()
            ->viewData('page')['props']['professionalSections'];

        $this->assertSame('Renommé depuis l’admin', $sections[0]['title']);
        $this->assertStringContainsString('Depuis 2040', (string) $sections[0]['eyebrow']);
    }

    /**
     * A date label is a display override, so the dates must survive being
     * overridden — otherwise clearing the label later would lose the ordering
     * the operator had already entered.
     */
    public function test_a_label_hides_the_dates_without_discarding_them(): void
    {
        $entry = ExperienceEntry::query()->where('locale', 'fr')->firstOrFail();

        $this->actingAs(User::factory()->create())
            ->put("/admin/experience/{$entry->id}", $this->position([
                'started_on' => '2011-06-01',
                'ended_on' => '2013-06-01',
                'date_label' => 'Avant 2014',
            ]))
            ->assertSessionHasNoErrors();

        $entry->refresh();

        $this->assertSame('Avant 2014', $entry->date_label);
        $this->assertSame('2011-06-01', $entry->started_on?->format('Y-m-d'));
        $this->assertSame('2013-06-01', $entry->ended_on?->format('Y-m-d'));
    }

    /**
     * The declaration refuses what Laravel's rules cannot see: an array is an
     * array either way, and only the schema knows a detail group needs a
     * title.
     */
    public function test_a_detail_group_missing_its_title_is_refused_by_the_declaration(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/admin/experience', $this->position([
                'detail_groups' => [['pills' => ['Laravel'], 'items' => ['Un point.']]],
            ]))
            ->assertSessionHasErrors('detail_groups');

        $this->assertSame(
            0,
            ExperienceEntry::query()->where('organisation', 'Atelier Cartographie')->count(),
            'A refused position was filed anyway.',
        );
    }

    public function test_an_end_date_before_the_start_is_refused(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/admin/experience', $this->position([
                'started_on' => '2026-02-01',
                'ended_on' => '2020-01-01',
            ]))
            ->assertSessionHasErrors('ended_on');
    }

    public function test_removing_a_position_removes_both_languages(): void
    {
        $entry = ExperienceEntry::query()->where('locale', 'fr')->firstOrFail();
        $key = $entry->translation_key;

        $this->actingAs(User::factory()->create())
            ->delete("/admin/experience/{$entry->id}")
            ->assertSessionHasNoErrors();

        $this->assertSame(
            0,
            ExperienceEntry::query()->where('translation_key', $key)->count(),
            'A position was removed in one language only.',
        );
    }

    public function test_the_index_lists_the_chronology_in_its_own_order(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin/experience?locale=fr')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Admin/Experience/Index')
                ->where('locale', 'fr')
                ->has('entries'));
    }
}
