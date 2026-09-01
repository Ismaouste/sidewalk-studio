<?php

namespace Tests\Feature;

use App\Models\Publication;
use Illuminate\Support\Facades\Schema;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PublicLanguageSwitcherTest extends TestCase
{
    public function test_supported_pages_expose_a_language_switcher_with_same_path_links(): void
    {
        $this->get('/en/experience')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('site.languageSwitcher.visible', true)
                ->where('site.languageSwitcher.current', 'en')
                ->where('site.languageSwitcher.options.0.code', 'en')
                ->where(
                    'site.languageSwitcher.options.0.href',
                    fn (string $href): bool => str_ends_with($href, '/en/experience'),
                )
                ->where('site.languageSwitcher.options.1.code', 'fr')
                ->where(
                    'site.languageSwitcher.options.1.href',
                    fn (string $href): bool => str_ends_with($href, '/fr/experience'),
                ));
    }

    public function test_newly_localized_contact_page_exposes_the_language_switcher(): void
    {
        $this->get('/en/contact')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('site.languageSwitcher.visible', true)
                ->where(
                    'site.languageSwitcher.options.1.href',
                    fn (string $href): bool => str_ends_with($href, '/fr/contact'),
                ));
    }

    public function test_writing_routes_expose_the_language_switcher_when_a_french_entry_exists(): void
    {
        $this->get('/en/journal')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('site.languageSwitcher.visible', true)
                ->where(
                    'site.languageSwitcher.options.1.href',
                    fn (string $href): bool => str_ends_with($href, '/fr/journal'),
                ));

        $this->get('/en/journal/content-systems-routing-and-metadata')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('site.languageSwitcher.visible', true)
                ->where(
                    'site.languageSwitcher.options.1.href',
                    fn (string $href): bool => str_ends_with($href, '/fr/journal/content-systems-routing-and-metadata'),
                ));
    }

    public function test_case_study_routes_expose_the_language_switcher_when_a_french_entry_exists(): void
    {
        $this->get('/en/case-studies')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('site.languageSwitcher.visible', true)
                ->where(
                    'site.languageSwitcher.options.1.href',
                    fn (string $href): bool => str_ends_with($href, '/fr/case-studies'),
                ));

        $this->get('/en/case-studies/pipeline-deploiement-ecommerce')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('site.languageSwitcher.visible', true)
                ->where(
                    'site.languageSwitcher.options.1.href',
                    fn (string $href): bool => str_ends_with($href, '/fr/case-studies/pipeline-deploiement-ecommerce'),
                ));
    }

    /**
     * Every publication in the tree happens to be translated, so the page
     * that used to stand in for "untranslated" here was `/en/labs` — which
     * stopped being one the moment its copy moved into the language files.
     * Borrowing an incidentally-untranslated page to test the rule means the
     * test dies the day that page gets translated, which is the day the rule
     * matters most. This builds the condition instead.
     */
    public function test_a_publication_with_no_translation_hides_the_language_switcher(): void
    {
        if (! Schema::hasTable('publications')) {
            $this->artisan('migrate:fresh', ['--seed' => true]);
        }

        Publication::query()->updateOrCreate([
            'type' => 'journal',
            'locale' => 'en',
            'slug' => 'english-only-entry',
        ], [
            'translation_key' => 'english-only-entry',
            'title' => 'An entry that exists in English only',
            'status' => 'published',
            'summary' => 'No French edition of this one exists.',
            'body_markdown' => 'Body.',
            'published_at' => '2026-03-07',
            'updated_at_publication' => '2026-03-07',
            'tags' => ['laravel'],
            'seo_title' => 'An entry that exists in English only',
            'seo_description' => 'No French edition of this one exists.',
            'robots' => 'index,follow',
            'category' => 'work',
            'accent_tone' => 'dominant',
            'metadata' => [],
            'source_driver' => 'database',
        ])->save();

        $this->get('/en/journal/english-only-entry')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('site.languageSwitcher.visible', false)
                ->where('site.languageSwitcher.options.1.available', false)
                ->where('site.languageSwitcher.options.1.href', null));
    }

    /**
     * The half of the pairing that was silently broken in both directions:
     * eight of the fifteen publications carry a French slug of their own, and
     * matching on the slug rather than `translation_key` hid the switcher on
     * the English article while offering, from the French one, an English
     * href built by swapping the prefix on a French slug — a 404 on a
     * published page.
     */
    public function test_a_publication_translated_under_another_slug_links_to_that_slug(): void
    {
        $this->get('/en/journal/technical-seo-sitemaps-and-structured-data-for-commerce')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('site.languageSwitcher.visible', true)
                ->where(
                    'site.languageSwitcher.options.1.href',
                    fn (string $href): bool => str_ends_with(
                        $href,
                        '/fr/journal/seo-technique-sitemaps-et-donnees-structurees',
                    ),
                ));

        $this->get('/fr/journal/seo-technique-sitemaps-et-donnees-structurees')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('site.languageSwitcher.visible', true)
                ->where(
                    'site.languageSwitcher.options.0.href',
                    fn (string $href): bool => str_ends_with(
                        $href,
                        '/en/journal/technical-seo-sitemaps-and-structured-data-for-commerce',
                    ),
                ));
    }

    /**
     * The href the switcher offers has to be a page, not just a well-formed
     * URL. This is the assertion the old slug-matching version could never
     * have passed.
     */
    public function test_every_offered_switcher_href_resolves(): void
    {
        $paths = [
            '/en/journal/technical-seo-sitemaps-and-structured-data-for-commerce',
            '/fr/journal/seo-technique-sitemaps-et-donnees-structurees',
            '/en/journal/njp-volunteering-and-small-tools',
            '/en/case-studies/pipeline-deploiement-ecommerce',
            '/en/services',
            '/en/labs',
        ];

        foreach ($paths as $path) {
            $response = $this->get($path)->assertOk();

            $switcher = $response->viewData('page')['props']['site']['languageSwitcher'];

            foreach ($switcher['options'] as $option) {
                if ($option['href'] === null) {
                    continue;
                }

                $this->get($option['href'])->assertOk();
            }
        }
    }
}
