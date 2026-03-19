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

    public function test_french_projects_page_uses_current_markdown_content(): void
    {
        $this->get('/fr/projects')
            ->assertOk()
            ->assertDontSee('Un goût pour les systèmes qui doivent vraiment tourner.')
            ->assertDontSee('{&quot;Delivery sur plusieurs niveaux à la fois&quot;')
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('hero.title', 'Des systèmes déjà en ligne, avec des contraintes réelles.')
                ->where(
                    'professionalSections.0.detail_groups.0.items.1',
                    'Le même niveau d\'attention sur des sites marchands, des sites de marque, des reprises d\'existant et des évolutions métier.',
                )
                ->where('professionalSections.0.title', 'Jewely / Flippad')
                ->where('associativeSections.0.title', 'Aremedia')
            );
    }

    public function test_draft_case_studies_stay_unreachable_even_if_a_database_record_exists(): void
    {
        if (! Schema::hasTable('publications')) {
            $this->artisan('migrate:fresh', ['--seed' => true]);
        }

        $record = Publication::query()
            ->where('type', 'case_study')
            ->where('locale', 'fr')
            ->where('slug', 'repo-bootstrap-foundation')
            ->firstOrFail();

        $record->forceFill([
            'status' => 'published',
            'summary' => 'Old database summary that should never leak publicly.',
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
