<?php

namespace Tests\Feature;

use App\Models\ExperienceEntry;
use App\Models\User;
use App\Services\ContentImportService;
use App\Services\QuestionnaireRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The door into the back office.
 *
 * It used to redirect to Settings — the screen an operator needs least often,
 * answering no question they arrived with. These tests are about the question
 * they did arrive with, and about the part that makes an answer useful: an
 * unfinished thing has to say what the public site does about it meanwhile.
 */
class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(ContentImportService::class)->importAll();
    }

    protected function digest(): array
    {
        return $this->actingAs(User::factory()->create())
            ->get('/admin')
            ->assertOk()
            ->viewData('page')['props'];
    }

    public function test_the_root_renders_the_dashboard_rather_than_the_settings_form(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Admin/Dashboard'));
    }

    /**
     * The unauthenticated branches are the reason this route sits outside the
     * guard, and they are unchanged.
     *
     * Onboarding is checked before the login form, and correctly: on a first
     * run there is no account to sign in to, so sending someone to a login
     * would be sending them to a dead end.
     */
    public function test_a_signed_out_visitor_is_still_sent_past_the_dashboard(): void
    {
        $this->get('/admin')->assertRedirect('/admin/onboarding');

        User::factory()->create();

        $this->get('/admin')->assertRedirect('/admin/login');
    }

    /**
     * Every item names a number, a place to go, and what the site does about
     * it in the meantime — the last being what separates a dashboard from a
     * row of badges.
     */
    public function test_every_unfinished_item_says_what_the_site_does_about_it(): void
    {
        $attention = $this->digest()['attention'];

        $this->assertNotEmpty($attention, 'A freshly seeded site has open work and reported none.');

        foreach ($attention as $item) {
            $this->assertGreaterThan(0, $item['count']);
            $this->assertNotSame('', trim($item['consequence']), "[{$item['key']}] says nothing about its consequence.");
            $this->assertStringStartsWith('/admin', $item['href']);
        }
    }

    public function test_the_unanswered_questionnaire_is_reported_and_then_stops_being(): void
    {
        $keys = array_column($this->digest()['attention'], 'key');
        $this->assertContains('questionnaire', $keys);

        $questionnaire = app(QuestionnaireRepository::class);

        foreach (['first_repair', 'changed_mind', 'owed_to_the_reader', 'what_a_system_says'] as $key) {
            $questionnaire->save($key, ['fr' => 'Une réponse.', 'en' => 'An answer.']);
        }

        $this->assertNotContains(
            'questionnaire',
            array_column($this->digest()['attention'], 'key'),
            'A fully answered questionnaire is still asking for attention.',
        );
    }

    /**
     * The count the page editor shows at the top of a form, computed on the
     * server so the dashboard can name it before anyone opens the page.
     *
     * `experience` is the fixture because it is the page that has them: eight
     * empty strings across its two widget groups, in each language.
     */
    public function test_declared_slots_nobody_filled_are_counted_per_page_and_language(): void
    {
        $item = collect($this->digest()['attention'])
            ->firstWhere('key', 'unfilled-slots');

        $this->assertNotNull($item, 'The unfilled declared slots were not reported.');

        $experience = collect($item['detail'])
            ->where('page_key', 'experience')
            ->pluck('count', 'locale');

        $this->assertSame(8, $experience['fr'] ?? null);
        $this->assertSame(8, $experience['en'] ?? null);
    }

    public function test_undated_positions_are_reported_while_they_have_no_start(): void
    {
        $this->assertContains(
            'undated-experience',
            array_column($this->digest()['attention'], 'key'),
        );

        ExperienceEntry::query()->update(['started_on' => '2020-01-01']);

        $this->assertNotContains(
            'undated-experience',
            array_column($this->digest()['attention'], 'key'),
        );
    }

    public function test_the_record_counts_one_language_rather_than_both(): void
    {
        $record = $this->digest()['record'];

        $this->assertSame(
            ExperienceEntry::query()->where('locale', 'en')->count(),
            $record['positions'],
            'The position count doubled because it counted both languages.',
        );
    }
}
