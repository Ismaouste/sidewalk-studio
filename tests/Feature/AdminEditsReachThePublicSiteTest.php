<?php

namespace Tests\Feature;

use App\Console\Commands\ExportStaticPreviewCommand;
use App\Content\Schema\PageSchemas;
use App\Models\Publication;
use App\Models\User;
use App\Services\ContentImportService;
use App\Services\ContentRepository;
use App\Services\PageContentRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * The thing the site could not do.
 *
 * `/admin` has been able to save page edits since it was built, and the
 * public site has been ignoring them since it was built — the repositories
 * preferred Markdown, deliberately, with two tests pinning the direction.
 * The reversal is the point of this spec, and these are the tests that say
 * so from the operator's side rather than the repository's.
 */
class AdminEditsReachThePublicSiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(ContentImportService::class)->importAll();
    }

    protected function operator(): User
    {
        return User::factory()->create();
    }

    /**
     * @return array<string, mixed>
     */
    protected function editablePayload(string $page, string $locale): array
    {
        $current = app(PageContentRepository::class)->adminFind($page, $locale);

        return [
            'title' => $current['title'] ?? '',
            'description' => $current['description'] ?? '',
            'seo_title' => $current['seo_title'],
            'seo_description' => $current['seo_description'],
            'robots' => $current['robots'] ?: 'index,follow',
            'canonical_url' => $current['canonical_url'] ?? '',
            'open_graph_image' => $current['open_graph_image'] ?? '',
            'payload' => $current['payload'],
        ];
    }

    public function test_a_page_saved_from_the_admin_changes_what_a_visitor_reads(): void
    {
        $payload = $this->editablePayload('colophon', 'en');
        $payload['payload']['hero']['title'] = 'Edited from the admin';

        $this->actingAs($this->operator())
            ->put('/admin/pages/colophon/en', $payload)
            ->assertRedirect('/admin/pages/colophon/en');

        $this->get('/en/colophon')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('hero.title', 'Edited from the admin'));
    }

    /**
     * The way back, and the reason an authoritative database is safe for
     * someone who does not write code.
     */
    public function test_an_operator_can_revert_a_page_to_its_markdown_seed(): void
    {
        $repository = app(PageContentRepository::class);
        $seededTitle = $repository->seededPage('colophon', 'en')['hero']['title'];

        $payload = $this->editablePayload('colophon', 'en');
        $payload['payload']['hero']['title'] = 'Edited from the admin';

        $operator = $this->operator();

        $this->actingAs($operator)->put('/admin/pages/colophon/en', $payload);

        $this->get('/en/colophon')
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('hero.title', 'Edited from the admin'));

        $this->actingAs($operator)
            ->post('/admin/pages/colophon/en/revert')
            ->assertRedirect('/admin/pages/colophon/en');

        $this->get('/en/colophon')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('hero.title', $seededTitle));

        $this->assertDatabaseHas('admin_audit_logs', ['action' => 'page.reverted']);
    }

    /**
     * The runtime replacement for the review-time checklist, on the surface
     * where drift is actually introduced.
     */
    public function test_a_save_that_would_desynchronise_the_two_locales_is_refused(): void
    {
        $payload = $this->editablePayload('colophon', 'en');

        // One section fewer than French holds. Shape drift, not a translation.
        array_pop($payload['payload']['sections']);

        $this->actingAs($this->operator())
            ->put('/admin/pages/colophon/en', $payload)
            ->assertSessionHasErrors('payload')
            /**
             * And the refusal has to survive the round trip to be worth
             * anything. Sessions here are cookie-backed, so anything over 4KB
             * is silently dropped by the browser — and Laravel's default
             * handling of a `ValidationException` reflashes the entire request
             * input alongside the errors. The entire request input is a page.
             *
             * The symptom was the worst available: the save was correctly
             * refused and the operator was told nothing at all. Nothing needs
             * the old input, because the form still holds what was typed.
             */
            ->assertSessionMissing('_old_input');

        $this->get('/en/colophon')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->has('sections', count(
                    app(PageContentRepository::class)->seededPage('colophon', 'fr')['sections'],
                )));
    }

    /**
     * The declaration is enforced at the save, not only in CI, and it is
     * enforced as a message rather than as a 500. A payload with a wrongly
     * typed value never becomes a row, and the operator is told which field.
     */
    public function test_a_save_that_does_not_match_the_declaration_is_refused(): void
    {
        $payload = $this->editablePayload('colophon', 'en');
        $payload['payload']['hero']['title'] = ['not' => 'a line'];

        $this->actingAs($this->operator())
            ->put('/admin/pages/colophon/en', $payload)
            ->assertSessionHasErrors('payload');

        $errors = session('errors')->get('payload');

        $this->assertContains(
            '[hero.title] should be a line, got a mapping (not).',
            $errors,
        );
    }

    /**
     * A violation the parity check cannot see, because both locales would
     * hold the same wrong shape. This is the case that would have reached
     * `savePage()` and come back as a 500 before the controller asked the
     * declaration directly.
     */
    public function test_an_undeclared_key_is_refused_with_a_message_rather_than_a_server_error(): void
    {
        $payload = $this->editablePayload('colophon', 'en');
        $payload['payload']['surprise'] = 'a key nothing declares';

        $this->actingAs($this->operator())
            ->put('/admin/pages/colophon/en', $payload)
            ->assertSessionHasErrors('payload');

        $this->assertContains(
            '[surprise] is not declared in the [colophon] schema.',
            session('errors')->get('payload'),
        );
    }

    /**
     * The static export follows too.
     *
     * `plan.md` predicted this and the prediction is worth pinning rather
     * than trusting: everything the export does goes through HTTP against a
     * running server, so rendering follows whatever the application reads.
     * The one coupling is the route list, which enumerates publication URLs
     * from the same repository — so a publication that exists only as a row
     * gets exported, and one unpublished from the admin stops being.
     */
    public function test_the_static_export_route_list_follows_the_database(): void
    {
        $command = app(ExportStaticPreviewCommand::class);
        $content = app(ContentRepository::class);

        $before = $command->routesToExport($content, 'en');

        $this->assertContains('/journal/ytmusic-liked-sorter', $before);

        Publication::query()
            ->where('locale', 'en')
            ->where('slug', 'ytmusic-liked-sorter')
            ->update(['status' => 'draft']);

        $after = $command->routesToExport($content, 'en');

        $this->assertNotContains(
            '/journal/ytmusic-liked-sorter',
            $after,
            'An entry unpublished from the admin is still being exported.',
        );
    }

    /**
     * A save that changes nothing must change nothing.
     *
     * This is the weakest promise an editor can make and it was not being
     * kept. Opening `experience` and pressing Save, touching no field, was
     * refused: `[associative_note_widget.eyebrow] should be a line, got
     * null`. The record holds eight empty strings across its two widget
     * groups and `contact` holds one, and Laravel's default
     * `ConvertEmptyStringsToNull` — which walks nested arrays — turned each
     * of them into a null on the way in. Four of the sixteen pairs could not
     * be saved at all, so the generated editor was read-only for the two
     * heaviest records on the site.
     *
     * Every test above this one uses `colophon`, which carries no empty
     * string and therefore never noticed. That is the whole reason this
     * iterates: the bug lived in the pages the suite did not name, and the
     * only honest fix is to stop naming one.
     *
     * Both halves are asserted. A refusal is caught by the session errors; a
     * silent rewrite — the more frightening failure, since an operator would
     * see a success message — is caught by comparing the stored payload.
     */
    public function test_every_page_and_locale_survives_a_save_that_changes_nothing(): void
    {
        $operator = $this->operator();
        $pages = app(PageContentRepository::class);

        foreach (PageSchemas::KEYS as $page) {
            foreach (['en', 'fr'] as $locale) {
                $before = $pages->adminFind($page, $locale)['payload'];

                $this->actingAs($operator)
                    ->put(
                        "/admin/pages/{$page}/{$locale}",
                        $this->editablePayload($page, $locale),
                    )
                    ->assertSessionHasNoErrors()
                    ->assertRedirect("/admin/pages/{$page}/{$locale}");

                $this->assertSame(
                    $before,
                    $pages->adminFind($page, $locale)['payload'],
                    "Saving [{$page}] in [{$locale}] without editing it changed the stored payload.",
                );
            }
        }
    }
}
