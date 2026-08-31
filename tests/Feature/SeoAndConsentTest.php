<?php

namespace Tests\Feature;

use App\Services\SiteSettingsService;
use Tests\TestCase;

class SeoAndConsentTest extends TestCase
{
    public function test_home_page_renders_server_side_metadata(): void
    {
        $description = app(SiteSettingsService::class)->current()->seoDefaults->defaultDescription;
        $canonical = rtrim((string) config('site.url'), '/').'/en';
        $ogImage = rtrim((string) config('site.url'), '/').'/images/og/site-default.jpg';

        $this->get('/en')
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.$canonical.'">', false)
            ->assertSee($description)
            ->assertSee('<meta property="og:image" content="'.$ogImage.'">', false)
            ->assertSee('application/ld+json', false);
    }

    public function test_projects_page_renders_breadcrumb_metadata(): void
    {
        $canonical = rtrim((string) config('site.url'), '/').'/en/projects';

        $this->get('/en/projects')
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.$canonical.'">', false)
            ->assertSee('Tech Lead Ecommerce in Nancy · Ismael Rodmacq')
            ->assertSee('"@type":"Person"', false)
            /**
             * Read from the setting rather than repeated here. The literal
             * this replaces had drifted: /projects was announcing a job title
             * the rest of the site had stopped using, and the test was
             * pinning the stale one.
             */
            ->assertSee('"jobTitle":'.json_encode(config('site.author.job_title')), false)
            ->assertSee('"addressRegion":"Grand Est"', false)
            ->assertSee('"email":"ismael@rodmacq.com"', false)
            ->assertSee('BreadcrumbList', false);
    }

    public function test_writing_article_page_renders_article_metadata_and_og_image(): void
    {
        $ogImage = rtrim((string) config('site.url'), '/').'/images/og/content-systems-routing-and-metadata.jpg';
        $canonical = rtrim((string) config('site.url'), '/').'/en/journal/content-systems-routing-and-metadata';

        $this->get('/en/journal/content-systems-routing-and-metadata')
            ->assertOk()
            ->assertSee('Content systems start with routing and metadata · Ismael Rodmacq')
            ->assertSee('<meta property="og:type" content="article">', false)
            ->assertSee('<meta property="og:image" content="'.$ogImage.'">', false)
            ->assertSee('<meta name="twitter:image" content="'.$ogImage.'">', false)
            ->assertSee('"@type":"Article"', false)
            ->assertSee('"@id":"'.$canonical.'"', false)
            ->assertSee('BreadcrumbList', false);
    }

    public function test_case_study_page_renders_creative_work_metadata(): void
    {
        $ogImage = rtrim((string) config('site.url'), '/').'/images/og/site-default.jpg';

        $this->get('/en/case-studies/pipeline-deploiement-ecommerce')
            ->assertOk()
            ->assertSee('Making a deployment pipeline honest in a live ecommerce environment · Ismael Rodmacq')
            ->assertSee('<meta property="og:type" content="website">', false)
            ->assertSee('<meta property="og:image" content="'.$ogImage.'">', false)
            ->assertSee('"@type":"CreativeWork"', false)
            ->assertSee('BreadcrumbList', false);
    }

    public function test_case_study_page_keeps_stable_canonical_url_when_resolved_in_french(): void
    {
        $canonical = rtrim((string) config('site.url'), '/').'/fr/case-studies/pipeline-deploiement-ecommerce';

        $this->withCookie('sidewalk_locale', 'fr')
            ->get('/fr/case-studies/pipeline-deploiement-ecommerce')
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.$canonical.'">', false)
            ->assertDontSee('hreflang', false)
            ->assertSee('Rendre un pipeline de déploiement honnête en environnement e-commerce · Ismaël Rodmacq');
    }

    public function test_contact_page_is_explicitly_noindex_follow(): void
    {
        $this->get('/en/contact')
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex,follow">', false);
    }

    public function test_sparkle_page_is_explicitly_noindex_nofollow(): void
    {
        $this->get('/en/sparkle')
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex,nofollow">', false)
            ->assertSee('Sparkle mode · Ismael Rodmacq');
    }

    public function test_default_description_stays_within_155_characters(): void
    {
        $description = app(SiteSettingsService::class)->current()->seoDefaults->defaultDescription;

        $this->assertLessThanOrEqual(155, mb_strlen($description));
    }

    public function test_labs_page_ships_embed_placeholder_without_loading_third_party_iframe(): void
    {
        $this->get('/en/labs')
            ->assertOk()
            ->assertSee('"service":"youtube"', false)
            ->assertDontSee('youtube-nocookie.com', false);
    }
}
