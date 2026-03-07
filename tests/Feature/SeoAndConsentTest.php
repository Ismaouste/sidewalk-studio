<?php

namespace Tests\Feature;

use App\Services\SiteSettingsService;
use Tests\TestCase;

class SeoAndConsentTest extends TestCase
{
    public function test_home_page_renders_server_side_metadata(): void
    {
        $description = app(SiteSettingsService::class)->current()->seoDefaults->defaultDescription;
        $canonical = rtrim((string) config('site.url'), '/');

        $this->get('/')
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.$canonical.'">', false)
            ->assertSee($description)
            ->assertSee('application/ld+json', false);
    }

    public function test_experience_page_renders_breadcrumb_metadata(): void
    {
        $canonical = rtrim((string) config('site.url'), '/').'/experience';

        $this->get('/experience')
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.$canonical.'">', false)
            ->assertSee('Experience | Sidewalk Studio')
            ->assertSee('BreadcrumbList', false);
    }

    public function test_case_study_page_renders_article_metadata(): void
    {
        $this->get('/case-studies/repo-bootstrap-foundation')
            ->assertOk()
            ->assertSee('Repository Bootstrap for a Spec-Driven Portfolio | Sidewalk Studio')
            ->assertSee('article', false)
            ->assertSee('BreadcrumbList', false);
    }

    public function test_case_study_page_keeps_stable_canonical_url_when_resolved_in_french(): void
    {
        $canonical = rtrim((string) config('site.url'), '/').'/case-studies/repo-bootstrap-foundation';

        $this->withCookie('sidewalk_locale', 'fr')
            ->get('/case-studies/repo-bootstrap-foundation')
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.$canonical.'">', false)
            ->assertDontSee('hreflang', false)
            ->assertSee('Bootstrap du repository pour un portfolio pilote par les specs | Sidewalk Studio');
    }

    public function test_labs_page_ships_embed_placeholder_without_loading_third_party_iframe(): void
    {
        $this->get('/labs')
            ->assertOk()
            ->assertSee('&quot;service&quot;:&quot;youtube&quot;', false)
            ->assertDontSee('youtube-nocookie.com', false);
    }
}
