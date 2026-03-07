<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    public function test_public_pages_are_reachable(): void
    {
        $pages = [
            '/' => 'Sidewalk Studio',
            '/about' => 'About | Sidewalk Studio',
            '/projects' => 'Projects | Sidewalk Studio',
            '/labs' => 'Labs | Sidewalk Studio',
            '/writing' => 'Writing | Sidewalk Studio',
            '/case-studies' => 'Case Studies | Sidewalk Studio',
            '/contact' => 'Contact | Sidewalk Studio',
        ];

        foreach ($pages as $url => $expectedText) {
            $this->get($url)
                ->assertOk()
                ->assertSee($expectedText);
        }
    }

    public function test_machine_readable_endpoints_are_available(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Sitemap: '.url('/sitemap.xml'));

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee('<?xml version="1.0" encoding="UTF-8"?>', false)
            ->assertSee(url('/writing/content-systems-routing-and-metadata'), false);
    }
}
