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

    public function test_it_prefers_markdown_over_database_overrides_for_public_pages(): void
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
        $payload['hero']['title'] = 'Old database hero title';

        $pageRecord->forceFill([
            'seo_title' => 'Old database seo title',
            'payload' => $payload,
        ])->save();

        $page = app(PageContentRepository::class)->get('projects', 'fr');

        $this->assertSame('Tech lead e-commerce à Nancy', $page['seo_title']);
        $this->assertSame(
            'Projets e-commerce',
            $page['hero']['title'],
        );
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
