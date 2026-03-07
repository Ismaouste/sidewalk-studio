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
            'Calm engineering for e-commerce, privacy, and durable products',
            $page['seo_title'],
        );
        $this->assertSame(
            'Calm engineering for e-commerce, privacy, and durable products.',
            $page['hero']['title'],
        );
    }

    public function test_it_falls_back_to_english_when_locale_file_is_missing(): void
    {
        app()->setLocale('fr');

        $page = app(PageContentRepository::class)->get('local');

        $this->assertSame('Local', $page['seo_title']);
        $this->assertSame('Local ground', $page['hero']['eyebrow']);
    }
}
