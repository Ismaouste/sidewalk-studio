<?php

namespace Tests\Feature;

use Tests\TestCase;

class SeoAndConsentTest extends TestCase
{
    public function test_home_page_renders_server_side_metadata(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('<link rel="canonical" href="http://sidewalk-studio.test">', false)
            ->assertSee('Engineering portfolio exploring privacy-first analytics, structured content systems, and refined Laravel + Inertia front-end craft.')
            ->assertSee('application/ld+json', false);
    }

    public function test_case_study_page_renders_article_metadata(): void
    {
        $this->get('/case-studies/repo-bootstrap-foundation')
            ->assertOk()
            ->assertSee('Repository Bootstrap for a Spec-Driven Portfolio | Sidewalk Studio')
            ->assertSee('article', false)
            ->assertSee('BreadcrumbList', false);
    }

    public function test_labs_page_ships_embed_placeholder_without_loading_third_party_iframe(): void
    {
        $this->get('/labs')
            ->assertOk()
            ->assertSee('&quot;service&quot;:&quot;youtube&quot;', false)
            ->assertDontSee('youtube-nocookie.com', false);
    }
}
