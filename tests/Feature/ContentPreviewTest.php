<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\User;
use App\Services\ContentImportService;
use App\Services\PageContentRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * The preview is the real page.
 *
 * `plan.md` is blunt about the alternative: for a site whose value is how it
 * looks, an in-editor mock is worse than nothing. So the draft is rendered by
 * the actual route, with the actual components, at the actual URL — and the
 * only thing that differs is which content the repository hands over.
 *
 * That design puts unpublished content behind a public URL, which is exactly
 * the kind of thing that leaks, so most of what is tested here is that it
 * does not.
 */
class ContentPreviewTest extends TestCase
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
    protected function draftedPayload(string $page, string $locale, string $heroTitle): array
    {
        $current = app(PageContentRepository::class)->adminFind($page, $locale);
        $payload = $current['payload'];
        $payload['hero']['title'] = $heroTitle;

        return [
            'title' => $current['title'] ?? '',
            'description' => $current['description'] ?? '',
            'seo_title' => $current['seo_title'],
            'seo_description' => $current['seo_description'],
            'robots' => $current['robots'] ?: 'index,follow',
            'canonical_url' => $current['canonical_url'] ?? '',
            'open_graph_image' => $current['open_graph_image'] ?? '',
            'payload' => $payload,
        ];
    }

    public function test_previewing_sends_the_operator_to_the_real_route_rendered_from_the_draft(): void
    {
        $operator = User::factory()->create();

        $this->actingAs($operator)
            ->post(
                '/admin/pages/colophon/en/preview',
                $this->draftedPayload('colophon', 'en', 'Only in the draft'),
            )
            ->assertRedirect('/en/colophon?preview=1');

        $this->actingAs($operator)
            ->get('/en/colophon?preview=1')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('hero.title', 'Only in the draft'));
    }

    /**
     * The guarantee that matters. A draft is unpublished content sitting
     * behind a public URL, and the query parameter is trivially guessable.
     */
    public function test_a_visitor_who_is_not_signed_in_never_sees_a_draft(): void
    {
        /**
         * The draft is written straight to the row rather than through the
         * admin route, so this test never authenticates at all. `actingAs`
         * persists across the requests of a single test — the first version
         * of this test signed in to create the draft and then "signed out" by
         * simply not calling `actingAs` again, which authenticated the guest
         * request too and reported a leak that was not there.
         */
        app(PageContentRepository::class)->saveDraft(
            'colophon',
            'en',
            $this->draftedPayload('colophon', 'en', 'Only in the draft'),
        );

        $published = Page::query()
            ->where('page_key', 'colophon')
            ->where('locale', 'en')
            ->firstOrFail()
            ->payload['hero']['title'];

        $this->assertNotSame('Only in the draft', $published);

        // Signed out, asking for it as loudly as possible.
        $this->get('/en/colophon?preview=1')
            ->assertOk()
            ->assertDontSee('Only in the draft')
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('hero.title', $published));
    }

    /**
     * Without the query parameter, even a signed-in operator gets the page as
     * everyone else sees it — otherwise the admin could not tell what is live.
     */
    public function test_a_signed_in_operator_sees_the_published_page_unless_they_ask_for_the_draft(): void
    {
        $operator = User::factory()->create();

        $this->actingAs($operator)->post(
            '/admin/pages/colophon/en/preview',
            $this->draftedPayload('colophon', 'en', 'Only in the draft'),
        );

        $this->actingAs($operator)
            ->get('/en/colophon')
            ->assertOk()
            ->assertDontSee('Only in the draft');
    }

    /**
     * Publishing settles the question the draft was asking. A draft left
     * behind would show an operator, at a URL they have bookmarked, a version
     * of the page that no longer exists anywhere.
     */
    public function test_saving_the_page_clears_its_draft(): void
    {
        $operator = User::factory()->create();

        $this->actingAs($operator)->post(
            '/admin/pages/colophon/en/preview',
            $this->draftedPayload('colophon', 'en', 'Only in the draft'),
        );

        $this->assertNotNull(
            Page::query()->where('page_key', 'colophon')->where('locale', 'en')
                ->firstOrFail()->draft_payload,
        );

        $this->actingAs($operator)->put(
            '/admin/pages/colophon/en',
            $this->draftedPayload('colophon', 'en', 'Published for real'),
        );

        $this->assertNull(
            Page::query()->where('page_key', 'colophon')->where('locale', 'en')
                ->firstOrFail()->draft_payload,
        );

        $this->actingAs($operator)
            ->get('/en/colophon?preview=1')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('hero.title', 'Published for real'));
    }

    /**
     * A preview is allowed to be wrong. It answers "does this look right",
     * which is worth asking about content that does not validate yet —
     * refusing to show an operator their own work until it passes would make
     * the check an obstacle rather than a guardrail. Publishing is where the
     * declaration and the locale parity apply.
     */
    public function test_a_draft_may_hold_content_that_could_not_be_published(): void
    {
        $operator = User::factory()->create();
        $payload = $this->draftedPayload('colophon', 'en', 'Half-written');
        array_pop($payload['payload']['sections']);

        $this->actingAs($operator)
            ->post('/admin/pages/colophon/en/preview', $payload)
            ->assertRedirect('/en/colophon?preview=1');

        $this->actingAs($operator)
            ->put('/admin/pages/colophon/en', $payload)
            ->assertSessionHasErrors('payload');
    }

    /**
     * `/projects` is rendered from two page keys, so both of them preview
     * there. It is the reason the unit of declaration is the page key and not
     * the route.
     */
    public function test_both_page_keys_that_compose_a_route_preview_at_that_route(): void
    {
        $operator = User::factory()->create();

        $this->actingAs($operator)
            ->post(
                '/admin/pages/projects/en/preview',
                $this->draftedPayload('projects', 'en', 'Drafted hero'),
            )
            ->assertRedirect('/en/experience?preview=1');

        $this->actingAs($operator)
            ->post(
                '/admin/pages/experience/en/preview',
                $this->draftedPayload('experience', 'en', 'Drafted hero'),
            )
            ->assertRedirect('/en/experience?preview=1');
    }
}
