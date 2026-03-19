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
            'Engineering for ecommerce, product data, and privacy',
            $page['seo_title'],
        );
        $this->assertSame(
            'Engineering for ecommerce, product data, and its flows.',
            $page['hero']['title'],
        );
    }

    public function test_it_loads_localized_page_content_when_locale_file_exists(): void
    {
        $page = app(PageContentRepository::class)->get('experience', 'fr');

        $this->assertSame('Parcours de tech lead e-commerce', $page['seo_title']);
        $this->assertSame(
            'Des systèmes déjà en ligne, avec du réel à tenir.',
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
