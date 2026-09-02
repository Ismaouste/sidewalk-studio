<?php

namespace Tests\Feature;

use App\Models\Publication;
use Illuminate\Support\Facades\Schema;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    public function test_public_pages_are_reachable(): void
    {
        $pages = [
            '/en' => 'Ismael Rodmacq',
            '/en/local' => 'Local · Ismael Rodmacq',
            '/en/experience' => 'Tech Lead Ecommerce in Nancy · Ismael Rodmacq',
            '/en/labs' => 'Labs · Ismael Rodmacq',
            '/en/services' => 'Services · Ismael Rodmacq',
            '/en/journal' => 'Journal · Ismael Rodmacq',
            '/en/case-studies' => 'Case Studies · Ismael Rodmacq',
            '/en/contact' => 'Contact · Ismael Rodmacq',
            '/en/colophon' => 'Colophon — how this site is built · Ismael Rodmacq',
        ];

        foreach ($pages as $url => $expectedText) {
            $this->get($url)
                ->assertOk()
                ->assertSee($expectedText);
        }
    }

    /**
     * Every public page answers without a locale prefix by sending the reader
     * to their own language.
     *
     * `/colophon` did not. It was the one page missing from that list, so a
     * typed or external link to it answered 404 while its seven siblings
     * redirected — and the static export, which fetches unprefixed paths,
     * could not export the page at all. Found by running a real export.
     */
    public function test_every_public_page_redirects_from_its_unprefixed_path(): void
    {
        $paths = [
            '/local',
            '/experience',
            '/labs',
            '/services',
            '/contact',
            '/data-processing',
            '/colophon',
            '/journal',
            '/case-studies',
        ];

        foreach ($paths as $path) {
            $this->get($path)->assertRedirect("/en{$path}");
        }
    }

    public function test_legacy_routes_redirect_to_the_experience_record(): void
    {
        $this->get('/about')
            ->assertRedirect('/en/experience');

        $this->get('/projects')
            ->assertRedirect('/en/experience');

        $this->get('/writing')
            ->assertRedirect('/en/journal');

        $this->get('/writing/content-systems-routing-and-metadata')
            ->assertRedirect('/en/journal/content-systems-routing-and-metadata');
    }

    /**
     * A retired address keeps the reader's language.
     *
     * `localizedPath()` falls back to `app()->getLocale()`, which is resolved
     * from headers and cookies before it is resolved from the URL — so a
     * French reader following an old `/fr/projects` link was answered with
     * `/en/experience`, losing the one thing that URL was certain about. The
     * locale has to come from the route.
     */
    public function test_the_retired_path_redirects_within_the_readers_language(): void
    {
        $this->get('/fr/projects')->assertRedirect('/fr/experience');
        $this->get('/en/projects')->assertRedirect('/en/experience');
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
            ->assertDontSee(url('/en/sparkle'), false)
            ->assertDontSee(url('/fr/sparkle'), false)
            ->assertSee(url('/en/experience'), false)
            ->assertSee(url('/fr/experience'), false)
            ->assertSee(url('/en/local'), false)
            ->assertSee(url('/fr/local'), false)
            ->assertSee(url('/en/contact'), false)
            ->assertSee(url('/fr/contact'), false)
            ->assertSee(url('/en/services'), false)
            ->assertSee(url('/fr/services'), false)
            ->assertSee(url('/en/journal'), false)
            ->assertSee(url('/fr/journal'), false)
            ->assertSee(url('/en/case-studies'), false)
            ->assertSee(url('/fr/case-studies'), false)
            ->assertSee(url('/en/journal/content-systems-routing-and-metadata'), false)
            ->assertSee(url('/fr/journal/content-systems-routing-and-metadata'), false);
    }

    public function test_hidden_sparkle_page_is_public_but_redirects_cleanly(): void
    {
        $this->get('/sparkle')
            ->assertRedirect('/en/sparkle');

        $this->get('/madeof')
            ->assertRedirect('/en/sparkle');

        $this->get('/fr/sparkle')
            ->assertOk()
            ->assertSee('Sparkle mode · Ismaël Rodmacq');
    }

    public function test_french_projects_page_uses_current_markdown_content(): void
    {
        $this->get('/fr/experience')
            ->assertOk()
            ->assertDontSee('Un goût pour les systèmes qui doivent vraiment tourner.')
            ->assertDontSee('{"Delivery sur plusieurs niveaux à la fois"', false)
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('hero.title', 'Projets e-commerce')
                ->where(
                    'professionalSections.0.detail_groups.0.items.0',
                    'Core partagé, thèmes par client, connecteurs métier, delivery continu et coordination entre ERP, catalogue et front.',
                )
                ->where('professionalSections.0.title', 'Jewely E-commerce')
                ->where('associativeSections.0.title', 'Aremedia')
            );
    }

    public function test_colophon_route_renders_with_sections_and_quote_prop(): void
    {
        $this->get('/fr/colophon')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Colophon')
                ->has('hero.title')
                ->has('sections', 4)
                ->has('closing.title')
            );

        $this->get('/en/colophon')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Colophon')
                ->has('sections', 4)
            );
    }

    public function test_projects_page_exposes_thesis_line_for_manifesto_opener(): void
    {
        $this->get('/fr/experience')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where(
                    'thesis',
                    'Développeur e-commerce. Je construis des plateformes qui vendent, je les tiens en production, et je les fais évoluer sans jamais fermer la boutique.',
                )
            );

        $this->get('/en/experience')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where(
                    'thesis',
                    'Ecommerce developer. I build platforms that sell, I keep them running in production, and I keep changing them without ever closing the shop.',
                )
            );
    }

    public function test_draft_case_studies_stay_unreachable_even_if_a_database_record_exists(): void
    {
        if (! Schema::hasTable('publications')) {
            $this->artisan('migrate:fresh', ['--seed' => true]);
        }

        Publication::query()->updateOrCreate([
            'type' => 'case_study',
            'locale' => 'fr',
            'slug' => 'repo-bootstrap-foundation',
        ], [
            'title' => 'Repository Bootstrap for a Spec-Driven Portfolio',
            'status' => 'published',
            'summary' => 'Old database summary that should never leak publicly.',
            'body_markdown' => 'Legacy publication body.',
            'published_at' => '2026-03-07',
            'updated_at_publication' => '2026-03-07',
            'tags' => ['laravel', 'architecture'],
            'seo_title' => 'Repository Bootstrap for a Spec-Driven Portfolio',
            'seo_description' => 'Legacy case study that should stay private once the markdown source is removed.',
            'robots' => 'index,follow',
            'category' => 'work',
            'accent_tone' => 'dominant',
            'metadata' => [
                'client' => 'Sidewalk Studio',
                'role' => 'Product, architecture, implementation',
                'stack' => ['Laravel 12'],
                'outcomes' => ['Legacy row'],
            ],
            'source_path' => resource_path('content/case-studies/fr/repo-bootstrap-foundation.md'),
            'source_driver' => 'hybrid',
        ])->save();

        $this->get('/fr/case-studies/repo-bootstrap-foundation')
            ->assertNotFound();
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
