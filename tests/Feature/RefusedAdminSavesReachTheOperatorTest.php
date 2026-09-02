<?php

namespace Tests\Feature;

use App\Http\Middleware\HandleInertiaRequests;
use App\Models\User;
use App\Services\ContentImportService;
use App\Services\PageContentRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * A refusal has to say so.
 *
 * Sessions here are cookie-backed — `SESSION_DRIVER=cookie`, in `.env` and in
 * `.env.example`, because the deployed runtime is serverless and has neither
 * a writable filesystem nor a database to keep them in. A cookie-backed
 * session must therefore survive a trip through `Set-Cookie`, and a browser
 * silently discards a cookie over roughly 4096 bytes. Silently: no error, no
 * console line, no failed request. The session simply does not come back.
 *
 * Laravel's exception handler answers a `ValidationException` by redirecting
 * with the errors *and the entire request input beside them*
 * (`Handler::invalid()`). On an admin page form the entire request input is a
 * page — tens of kilobytes for `experience`. So the cookie was dropped, the
 * errors went with it, and the operator watched a save do nothing and say
 * nothing. The worst symptom available: the refusal was correct, and invisible.
 *
 * `AdminPageController::update` was fixed at the time by routing around the
 * exception. That fixed one action. This test pins the guarantee for all of
 * them, at the boundary where the input is flashed, because `preview` and the
 * language-file editor validate a `payload` too and still took the default
 * path.
 *
 * Nothing is lost by dropping that input: the application never reads it.
 * There is no `old()` call and no `withInput()` call anywhere in `app/` or
 * `resources/views/` — the forms are Inertia forms, and an Inertia form still
 * holds what was typed in the browser that typed it.
 */
class RefusedAdminSavesReachTheOperatorTest extends TestCase
{
    use RefreshDatabase;

    /**
     * What a browser will drop. The real ceiling applies to the whole
     * `Set-Cookie` header, and Laravel encrypts and base64-encodes the
     * session on the way out, so the serialized session has to stay a good
     * deal under this to survive. The assertions below use a fraction of it.
     */
    protected const COOKIE_CEILING = 4096;

    protected function setUp(): void
    {
        parent::setUp();

        app(ContentImportService::class)->importAll();
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

    public function test_a_refused_preview_returns_its_errors_without_the_page_beside_them(): void
    {
        $form = $this->editablePayload('experience', 'fr');

        // The hazard, stated rather than assumed: this one field is larger on
        // its own than anything a browser will carry back.
        $this->assertGreaterThan(
            self::COOKIE_CEILING,
            strlen((string) json_encode($form['payload'])),
            'This test is only meaningful while the page payload exceeds what a cookie holds.',
        );

        $form['seo_title'] = str_repeat('a', 200);

        $response = $this
            ->actingAs(User::factory()->create())
            ->post('/admin/pages/experience/fr/preview', $form);

        $response->assertSessionHasErrors('seo_title');
        $response->assertSessionMissing('_old_input.payload');

        $this->assertLessThan(
            self::COOKIE_CEILING / 2,
            strlen(serialize(session()->all())),
            'The session carrying a refusal has to fit in a cookie with room to spare.',
        );
    }

    /**
     * Every violation reaches the browser, not just the first.
     *
     * A page save is refused by comparing a whole document against its
     * declaration and against the other locale, so one refusal routinely
     * carries several violations at once. Inertia collapses each key's
     * messages to `$errors[0]` unless `$withAllErrors` is set, and that
     * switch is all-or-nothing across the application -- every other admin
     * form is written against a string. So the reasons a save was refused
     * arrived as one, and an operator would have fixed them one save at a
     * time, learning the next only after fixing the last.
     *
     * `Admin/Pages/Edit.vue` was already written for the list: its
     * `payloadErrors` does `if (Array.isArray(value)) return value`. It could
     * simply never receive one.
     */
    public function test_a_refusal_carrying_several_violations_delivers_all_of_them(): void
    {
        $form = $this->editablePayload('home', 'fr');

        // Several separate departures from the declaration, in one save.
        unset($form['payload']['hero']['title']);
        unset($form['payload']['contact_cta']);
        $form['payload']['a_key_the_declaration_does_not_have'] = 'x';

        $response = $this
            ->actingAs(User::factory()->create())
            ->from('/admin/pages/home/fr')
            ->put('/admin/pages/home/fr', $form);

        $response->assertRedirect();

        $violations = session('errors')->get('payload');

        $this->assertGreaterThan(
            1,
            count($violations),
            'This test is only meaningful while the refusal carries several violations.',
        );

        $delivered = app(HandleInertiaRequests::class)
            ->resolveValidationErrors(request());

        $this->assertIsArray(
            $delivered->payload,
            'Every violation has to reach the browser, not just the first.',
        );
        $this->assertSame($violations, $delivered->payload);
    }

    /**
     * The narrowing that keeps the change safe: only `payload` becomes a
     * list. Every other key stays the string the rest of the admin is
     * written against, which is why this cannot be `$withAllErrors`.
     */
    public function test_other_fields_still_arrive_as_a_single_string(): void
    {
        $form = $this->editablePayload('experience', 'fr');
        $form['seo_title'] = str_repeat('a', 200);

        $this
            ->actingAs(User::factory()->create())
            ->post('/admin/pages/experience/fr/preview', $form);

        $delivered = app(HandleInertiaRequests::class)
            ->resolveValidationErrors(request());

        $this->assertIsString($delivered->seo_title);
    }

    public function test_the_operator_is_told_which_field_was_refused(): void
    {
        $form = $this->editablePayload('experience', 'fr');
        $form['seo_title'] = str_repeat('a', 200);

        $this
            ->actingAs(User::factory()->create())
            ->post('/admin/pages/experience/fr/preview', $form)
            ->assertRedirect();

        $errors = session('errors');

        $this->assertNotNull($errors, 'A refused save must leave errors in the session.');
        $this->assertTrue(
            $errors->has('seo_title'),
            'The refusal must name the field it refused.',
        );
    }
}
