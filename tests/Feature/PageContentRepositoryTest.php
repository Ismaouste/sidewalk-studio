<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Services\PageContentRepository;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PageContentRepositoryTest extends TestCase
{
    public function test_it_loads_page_content_from_markdown(): void
    {
        $page = app(PageContentRepository::class)->get('home');

        $this->assertSame(
            'Ecommerce developer in Nancy',
            $page['seo_title'],
        );
        $this->assertSame(
            'Ismael Rodmacq',
            $page['hero']['title'],
        );
    }

    public function test_it_loads_localized_page_content_when_locale_file_exists(): void
    {
        $page = app(PageContentRepository::class)->get('experience', 'fr');

        $this->assertSame('Parcours de tech lead e-commerce', $page['seo_title']);
        $this->assertSame(
            'Projets et expérience',
            $page['hero']['title'],
        );
        $this->assertSame(
            'Repères techniques',
            $page['career_snapshot']['title'],
        );
    }

    /**
     * The inverse of the assertion this test used to make, and the point of
     * the whole spec: an edit saved from /admin now changes the public page.
     *
     * It used to pin Markdown as the winner. That was a real decision —
     * versioned content should not be silently overwritten by a stale row —
     * and its consequence was that the admin saved page edits the site
     * ignored. The Markdown is still there and still the seed; it is now what
     * an operator reverts to rather than what overrules them.
     */
    public function test_a_database_page_overrides_the_markdown_it_was_seeded_from(): void
    {
        // Checks for the seeded row as well as the table, for the reason
        // spelled out in ContentRepositoryTest: after a RefreshDatabase case
        // the table is present and empty, which `Schema::hasTable` alone
        // reports as fine.
        $seededPage = fn () => Page::query()
            ->where('page_key', 'projects')
            ->where('locale', 'fr');

        if (! Schema::hasTable('pages') || $seededPage()->doesntExist()) {
            $this->artisan('migrate:fresh', ['--seed' => true]);
        }

        $pageRecord = $seededPage()->firstOrFail();

        $payload = $pageRecord->payload ?? [];
        $payload['hero']['title'] = 'Hero title edited from the admin';

        $pageRecord->forceFill([
            'seo_title' => 'SEO title edited from the admin',
            'payload' => $payload,
        ])->save();

        $repository = app(PageContentRepository::class);
        $page = $repository->get('projects', 'fr');

        $this->assertSame('SEO title edited from the admin', $page['seo_title']);
        $this->assertSame('Hero title edited from the admin', $page['hero']['title']);

        // And the seed is still addressable, which is the revert path.
        $seed = $repository->seededPage('projects', 'fr');

        $this->assertSame('Tech lead e-commerce à Nancy', $seed['seo_title']);
        $this->assertSame('Projets e-commerce', $seed['hero']['title']);
    }

    /**
     * A page the database does not hold still renders. This is what makes the
     * new default safe on a deployment with no database at all — the Vercel
     * one, where SQLite is not in the repository.
     */
    public function test_a_page_missing_from_the_database_still_renders_from_markdown(): void
    {
        if (Schema::hasTable('pages')) {
            Page::query()->where('page_key', 'colophon')->delete();
        }

        $page = app(PageContentRepository::class)->get('colophon', 'en');

        $this->assertSame('file', $page['source_driver']);
        $this->assertNotSame('', $page['seo_title']);
    }

    public function test_it_loads_newly_localized_contact_page_content(): void
    {
        $page = app(PageContentRepository::class)->get('contact', 'fr');

        $this->assertSame('Contact', $page['seo_title']);
        $this->assertSame(
            'Ouvrir WhatsApp',
            $page['form']['primary_cta'],
        );
        $this->assertSame(
            'Repères rapides',
            $page['recruiter_shortcut']['eyebrow'],
        );
    }

    public function test_it_falls_back_to_english_when_locale_file_is_missing(): void
    {
        $page = app(PageContentRepository::class)->get('local', 'de');

        $this->assertSame('Local', $page['seo_title']);
        $this->assertSame('Local ground', $page['hero']['eyebrow']);
    }
}
