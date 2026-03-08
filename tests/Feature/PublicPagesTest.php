<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    public function test_public_pages_are_reachable(): void
    {
        $pages = [
            '/' => 'Sidewalk Studio',
            '/local' => 'Local | Sidewalk Studio',
            '/projects' => 'Experience | Sidewalk Studio',
            '/labs' => 'Labs | Sidewalk Studio',
            '/journal' => 'Journal | Sidewalk Studio',
            '/case-studies' => 'Case Studies | Sidewalk Studio',
            '/contact' => 'Contact | Sidewalk Studio',
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
            ->assertRedirect('/projects');

        $this->get('/experience')
            ->assertRedirect('/projects');

        $this->get('/writing')
            ->assertRedirect('/journal');

        $this->get('/writing/content-systems-routing-and-metadata')
            ->assertRedirect('/journal/content-systems-routing-and-metadata');
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
            ->assertSee(url('/projects'), false)
            ->assertSee(url('/local'), false)
            ->assertSee(url('/journal/content-systems-routing-and-metadata'), false);
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
