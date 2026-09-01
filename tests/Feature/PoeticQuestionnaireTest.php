<?php

namespace Tests\Feature;

use App\Models\QuestionnaireAnswer;
use App\Models\User;
use App\Services\ContentImportService;
use App\Services\QuestionnaireRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The questions the site asks its owner, and where the answers land.
 *
 * The first test is the one that made this safe to ship: with nothing
 * answered, the public page is exactly what it was. Everything else here is
 * about a feature that is allowed to be half-finished forever.
 */
class PoeticQuestionnaireTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(ContentImportService::class)->importAll();
    }

    protected function spreads(string $locale = 'fr'): array
    {
        return $this->get("/{$locale}/experience")
            ->assertOk()
            ->viewData('page')['props']['professionalSections'];
    }

    public function test_an_unanswered_questionnaire_leaves_the_page_untouched(): void
    {
        foreach ($this->spreads() as $spread) {
            $this->assertArrayNotHasKey(
                'marginalia',
                $spread,
                'An empty questionnaire put a marginal note on the page.',
            );
        }
    }

    /**
     * The slot `EditorialSpread` has carried since it was written, finally
     * given a source — and the caption is the question, which is what makes
     * it read as a Q&A rather than as a quote from nowhere.
     */
    public function test_an_answer_becomes_a_marginal_note_beside_the_first_position(): void
    {
        app(QuestionnaireRepository::class)->save('first_repair', [
            'fr' => 'Le nom des choses.',
            'en' => 'The names of things.',
        ]);

        $french = $this->spreads('fr');

        $this->assertSame('Le nom des choses.', $french[0]['marginalia']['quote']);
        $this->assertSame(
            'Que répare-t-on en premier, dans un système dont on hérite ?',
            $french[0]['marginalia']['prompt'],
            'The caption is not the question, in the reader’s language.',
        );

        $this->assertArrayNotHasKey(
            'marginalia',
            $french[1],
            'One answer filled more than one spread.',
        );

        $english = $this->spreads('en');

        $this->assertSame('The names of things.', $english[0]['marginalia']['quote']);
        $this->assertSame(
            'What do you repair first, in a system you inherit?',
            $english[0]['marginalia']['prompt'],
        );
    }

    /**
     * Order is the declaration's, not the database's, so the questions land
     * against the chronology in the order they were written to be read in.
     */
    public function test_answered_questions_land_in_declaration_order(): void
    {
        $questionnaire = app(QuestionnaireRepository::class);
        $questionnaire->save('first_repair', ['fr' => 'Premier.', 'en' => 'First.']);
        $questionnaire->save('changed_mind', ['fr' => 'Deuxième.', 'en' => 'Second.']);

        $spreads = $this->spreads();

        $this->assertSame('Premier.', $spreads[0]['marginalia']['quote']);
        $this->assertSame('Deuxième.', $spreads[1]['marginalia']['quote']);
    }

    /**
     * Skipped rather than held open: a question answered in second place
     * still takes the first note, because a gap on the page would read as a
     * bug rather than as a choice.
     */
    public function test_an_unanswered_question_is_skipped_rather_than_left_as_a_hole(): void
    {
        app(QuestionnaireRepository::class)->save('changed_mind', [
            'fr' => 'La seule répondue.',
            'en' => 'The only answered one.',
        ]);

        $spreads = $this->spreads();

        $this->assertSame('La seule répondue.', $spreads[0]['marginalia']['quote']);
    }

    /**
     * An empty answer is not a stored blank.
     *
     * Keeping both an absent row and a present empty string would mean every
     * read has to know the difference — which is exactly what made four admin
     * pages unsaveable elsewhere in this codebase.
     */
    public function test_clearing_an_answer_removes_the_row_rather_than_blanking_it(): void
    {
        $questionnaire = app(QuestionnaireRepository::class);
        $questionnaire->save('first_repair', ['fr' => 'Quelque chose.', 'en' => 'Something.']);

        $this->assertSame(2, QuestionnaireAnswer::query()->count());

        $questionnaire->save('first_repair', ['fr' => '   ', 'en' => '']);

        $this->assertSame(
            0,
            QuestionnaireAnswer::query()->count(),
            'A cleared answer was stored as a blank row.',
        );
    }

    public function test_the_admin_saves_every_question_in_one_request(): void
    {
        $this->actingAs(User::factory()->create())
            ->put('/admin/questionnaire', [
                'answers' => [
                    'first_repair' => ['fr' => 'Le nom des choses.', 'en' => 'The names of things.'],
                    'what_a_system_says' => ['fr' => 'Ce qu’il fait.', 'en' => 'What it does.'],
                    'not_a_declared_question' => ['fr' => 'Ignoré.', 'en' => 'Ignored.'],
                ],
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/questionnaire');

        $this->assertSame(4, QuestionnaireAnswer::query()->count());
        $this->assertSame(
            0,
            QuestionnaireAnswer::query()
                ->where('question_key', 'not_a_declared_question')
                ->count(),
            'An undeclared question was stored.',
        );
    }

    /**
     * The cap is a design constraint, not a storage one: the answer lands in
     * a micro-typographic caption, and a paragraph there stops being a
     * marginal note.
     */
    public function test_an_answer_longer_than_a_marginal_note_is_refused(): void
    {
        $this->actingAs(User::factory()->create())
            ->put('/admin/questionnaire', [
                'answers' => [
                    'first_repair' => ['fr' => str_repeat('a', 281), 'en' => 'Fine.'],
                ],
            ])
            ->assertSessionHasErrors('answers.first_repair.fr');

        $this->assertSame(0, QuestionnaireAnswer::query()->count());
    }

    public function test_the_admin_screen_lists_every_declared_question(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin/questionnaire')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Admin/Questionnaire/Index')
                ->has('questions', 4)
                ->where('unanswered', 8));
    }
}
