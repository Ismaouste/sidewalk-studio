<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    public function test_public_pages_are_reachable(): void
    {
        $pages = [
            '/en' => 'Ismael Rodmacq',
            '/en/local' => 'Local · Ismael Rodmacq',
            '/en/projects' => 'Tech Lead Ecommerce in Nancy · Ismael Rodmacq',
            '/en/labs' => 'Labs · Ismael Rodmacq',
            '/en/journal' => 'Journal · Ismael Rodmacq',
            '/en/case-studies' => 'Case Studies · Ismael Rodmacq',
            '/en/contact' => 'Contact · Ismael Rodmacq',
        ];

        foreach ($pages as $url => $expectedText) {
            $this->get($url)
                ->assertOk()
                ->assertSee($expectedText);
        }
    }

    public function test_legacy_routes_redirect_to_projects(): void
    {
        $this->get('/about')
            ->assertRedirect('/en/projects');

        $this->get('/experience')
            ->assertRedirect('/en/projects');

        $this->get('/writing')
            ->assertRedirect('/en/journal');

        $this->get('/writing/content-systems-routing-and-metadata')
            ->assertRedirect('/en/journal/content-systems-routing-and-metadata');
    }

    public function test_machine_readable_endpoints_are_available(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Sitemap: '.url('/sitemap.xml'));

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee('<?xml version="1.0" encoding="UTF-8"?>', false)
            ->assertDontSee(url('/experience'), false)
            ->assertDontSee(url('/en/labs'), false)
            ->assertSee(url('/en/projects'), false)
            ->assertSee(url('/fr/projects'), false)
            ->assertSee(url('/en/local'), false)
            ->assertSee(url('/fr/local'), false)
            ->assertSee(url('/en/contact'), false)
            ->assertSee(url('/fr/contact'), false)
            ->assertSee(url('/en/journal'), false)
            ->assertSee(url('/fr/journal'), false)
            ->assertSee(url('/en/case-studies'), false)
            ->assertSee(url('/fr/case-studies'), false)
            ->assertSee(url('/en/journal/content-systems-routing-and-metadata'), false)
            ->assertSee(url('/fr/journal/content-systems-routing-and-metadata'), false);
    }

    public function test_placeholder_visual_endpoints_are_publicly_available(): void
    {
        $this->get('/content-visuals/writing/content-systems-routing-and-metadata.svg')
            ->assertOk()
            ->assertHeader('content-type', 'image/svg+xml')
            ->assertSee('<svg', false);

        $this->get('/content-visuals/writing/opensurvey-associatif-donnees-sante.svg')
            ->assertOk()
            ->assertHeader('content-type', 'image/svg+xml')
            ->assertSee('<svg', false);
    }
}
