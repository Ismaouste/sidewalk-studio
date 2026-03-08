<?php

namespace Tests\Feature;

use App\Services\PageContentRepository;
use Tests\TestCase;

class PageContentRepositoryTest extends TestCase
{
    public function test_it_loads_page_content_from_markdown(): void
    {
        $page = app(PageContentRepository::class)->get('home');

        $this->assertSame(
            'Engineering for e-commerce, product data, and privacy',
            $page['seo_title'],
        );
        $this->assertSame(
            'Engineering for e-commerce, product data, and its flows.',
            $page['hero']['title'],
        );
    }

    public function test_it_loads_localized_page_content_when_locale_file_exists(): void
    {
        $page = app(PageContentRepository::class)->get('experience', 'fr');

        $this->assertSame('Parcours', $page['seo_title']);
        $this->assertSame(
            'Un goût pour les systèmes qui doivent vraiment tourner.',
            $page['hero']['title'],
        );
        $this->assertSame(
            'Repères techniques',
            $page['career_snapshot']['title'],
        );
    }

    public function test_it_loads_newly_localized_contact_page_content(): void
    {
        $page = app(PageContentRepository::class)->get('contact', 'fr');

        $this->assertSame('Contact', $page['seo_title']);
        $this->assertSame(
            'Envoyer le message',
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
