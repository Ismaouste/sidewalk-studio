<?php

namespace Tests\Feature;

use App\Http\Middleware\ResolvePublicLocale;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PublicLocaleResolutionTest extends TestCase
{
    public function test_browser_language_prefers_french_page_content(): void
    {
        $canonical = rtrim((string) config('site.url'), '/').'/fr/projects';
        $response = $this->followingRedirects()->withHeaders([
            'Accept-Language' => 'fr-FR,fr;q=0.9,en;q=0.8',
        ])->get('/projects');

        $this->assertStringContainsString(
            'Accept-Language',
            (string) $response->headers->get('vary'),
        );
        $this->assertStringContainsString(
            'Cookie',
            (string) $response->headers->get('vary'),
        );

        $response->assertOk()
            ->assertHeader('content-language', 'fr')
            ->assertSee('<link rel="canonical" href="'.$canonical.'">', false)
            ->assertDontSee('hreflang', false)
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('hero.title', 'Projets e-commerce')
                ->where('site.locale', 'fr')
                ->where('seo.canonical', $canonical)
                ->where('seo.openGraph.locale', 'fr'));
    }

    public function test_explicit_lang_query_persists_locale_preference(): void
    {
        $this->get('/local?lang=fr')
            ->assertRedirect('/fr/local')
            ->assertCookie(ResolvePublicLocale::COOKIE_NAME, 'fr');

        $this->withCookie(ResolvePublicLocale::COOKIE_NAME, 'fr')
            ->get('/fr')
            ->assertOk()
            ->assertHeader('content-language', 'fr')
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('hero.eyebrow', 'Développeur e-commerce · Nancy')
                ->where('site.locale', 'fr'));
    }

    public function test_legacy_public_query_parameters_are_redirected_to_clean_canonical_urls(): void
    {
        $this->get('/fr/projects?path=fr%2Fprojects')
            ->assertRedirect('/fr/projects');

        $this->get('/projects?lang=fr&path=projects')
            ->assertRedirect('/fr/projects')
            ->assertCookie(ResolvePublicLocale::COOKIE_NAME, 'fr');
    }

    public function test_vercel_rewrite_path_query_is_not_treated_as_legacy_public_redirect(): void
    {
        $this->withHeaders([
            'X-Vercel-Id' => 'cdg1::test-request',
        ])->get('/fr/projects?path=fr%2Fprojects')
            ->assertOk()
            ->assertHeader('content-language', 'fr')
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('site.locale', 'fr'))
            // Inertia 3 ships the page object as raw JSON inside
            // <script type="application/json">, not as an HTML-escaped
            // data-page attribute, so these assert on unescaped JSON.
            ->assertSee('"url":"\/fr\/projects"', false)
            ->assertDontSee('"url":"\/fr\/projects?path=fr%2Fprojects"', false);
    }

    public function test_newly_localized_contact_page_renders_french_content(): void
    {
        $this->withCookie(ResolvePublicLocale::COOKIE_NAME, 'fr')
            ->get('/fr/contact')
            ->assertOk()
            ->assertHeader('content-language', 'fr')
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('site.locale', 'fr')
                ->where('hero.title', "Parler d'un poste, d'une mission ou d'un sujet précis.")
                ->where('site.languageSwitcher.visible', true));
    }

    public function test_pages_without_french_source_stay_on_english_even_with_french_preference(): void
    {
        $this->withCookie(ResolvePublicLocale::COOKIE_NAME, 'fr')
            ->get('/fr/labs')
            ->assertOk()
            ->assertHeader('content-language', 'en')
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('site.locale', 'en')
                ->where('site.languageSwitcher.visible', false));
    }

    public function test_writing_index_renders_french_entries_when_french_locale_is_resolved(): void
    {
        $canonical = rtrim((string) config('site.url'), '/').'/fr/journal';

        $this->withCookie(ResolvePublicLocale::COOKIE_NAME, 'fr')
            ->get('/fr/journal')
            ->assertOk()
            ->assertHeader('content-language', 'fr')
            ->assertSee('<link rel="canonical" href="'.$canonical.'">', false)
            ->assertDontSee('hreflang', false)
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('site.locale', 'fr')
                ->where(
                    'items',
                    fn ($items): bool => collect($items)->every(
                        fn (array $item): bool => $item['locale'] === 'fr',
                    ),
                )
                ->where(
                    'items',
                    fn ($items): bool => collect($items)->contains(
                        fn (array $item): bool => $item['slug'] === 'quand-un-deploiement-reussi-ne-lest-pas',
                    ),
                )
                ->where('site.languageSwitcher.visible', true)
                ->where('seo.canonical', $canonical));
    }

    public function test_writing_detail_renders_localized_french_entry_with_stable_canonical_url(): void
    {
        $canonical = rtrim((string) config('site.url'), '/').'/fr/journal/content-systems-routing-and-metadata';

        $this->withCookie(ResolvePublicLocale::COOKIE_NAME, 'fr')
            ->get('/fr/journal/content-systems-routing-and-metadata')
            ->assertOk()
            ->assertHeader('content-language', 'fr')
            ->assertSee('<link rel="canonical" href="'.$canonical.'">', false)
            ->assertDontSee('hreflang', false)
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('site.locale', 'fr')
                ->where('item.locale', 'fr')
                ->where('item.title', 'Les systèmes de contenu commencent par le routage et les métadonnées')
                ->where('seo.canonical', $canonical)
                ->where('seo.openGraph.locale', 'fr'));
    }

    public function test_writing_detail_falls_back_to_english_when_no_french_entry_exists(): void
    {
        $path = resource_path('content/writing/en/editorial-english-fallback-public-test.md');

        file_put_contents($path, <<<'MD'
---
title: Editorial English Fallback Public Test
slug: editorial-english-fallback-public-test
summary: This published writing entry exists only in English and should stay safely reachable.
status: published
published_at: 2026-03-08
updated_at: 2026-03-08
tags:
    - content
    - fallback
seo_title: Editorial English Fallback Public Test
seo_description: This entry proves public writing routes still fall back to English when a French translation does not exist.
---

This English-only public writing entry should stay reachable even when the preferred locale is French.
MD);

        try {
            $canonical = rtrim((string) config('site.url'), '/').'/en/journal/editorial-english-fallback-public-test';

            $this->withCookie(ResolvePublicLocale::COOKIE_NAME, 'fr')
                ->get('/fr/journal/editorial-english-fallback-public-test')
                ->assertOk()
                ->assertHeader('content-language', 'en')
                ->assertSee('<link rel="canonical" href="'.$canonical.'">', false)
                ->assertInertia(fn (Assert $page): Assert => $page
                    ->where('site.locale', 'en')
                    ->where('item.locale', 'en')
                    ->where('site.languageSwitcher.visible', false)
                    ->where('seo.canonical', $canonical));
        } finally {
            unlink($path);
        }
    }

    public function test_case_study_index_renders_french_entries_when_french_locale_is_resolved(): void
    {
        $canonical = rtrim((string) config('site.url'), '/').'/fr/case-studies';

        $this->withCookie(ResolvePublicLocale::COOKIE_NAME, 'fr')
            ->get('/fr/case-studies')
            ->assertOk()
            ->assertHeader('content-language', 'fr')
            ->assertSee('<link rel="canonical" href="'.$canonical.'">', false)
            ->assertDontSee('hreflang', false)
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('site.locale', 'fr')
                ->where(
                    'items',
                    fn ($items): bool => collect($items)->every(
                        fn (array $item): bool => $item['locale'] === 'fr',
                    ),
                )
                ->where(
                    'items',
                    fn ($items): bool => collect($items)->contains(
                        fn (array $item): bool => $item['slug'] === 'pipeline-deploiement-ecommerce',
                    ),
                )
                ->where('site.languageSwitcher.visible', true)
                ->where('seo.canonical', $canonical));
    }

    public function test_case_study_detail_renders_localized_french_entry_with_stable_canonical_url(): void
    {
        $canonical = rtrim((string) config('site.url'), '/').'/fr/case-studies/pipeline-deploiement-ecommerce';

        $this->withCookie(ResolvePublicLocale::COOKIE_NAME, 'fr')
            ->get('/fr/case-studies/pipeline-deploiement-ecommerce')
            ->assertOk()
            ->assertHeader('content-language', 'fr')
            ->assertSee('<link rel="canonical" href="'.$canonical.'">', false)
            ->assertDontSee('hreflang', false)
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('site.locale', 'fr')
                ->where('item.locale', 'fr')
                ->where('item.title', 'Rendre un pipeline de déploiement honnête en environnement e-commerce')
                ->where('seo.canonical', $canonical)
                ->where('seo.openGraph.locale', 'fr'));
    }

    public function test_case_study_detail_falls_back_to_english_when_no_french_entry_exists(): void
    {
        $path = resource_path('content/case-studies/en/case-study-english-fallback-public-test.md');

        file_put_contents($path, <<<'MD'
---
title: Case Study English Fallback Public Test
slug: case-study-english-fallback-public-test
summary: This published case study exists only in English and should stay safely reachable.
status: published
published_at: 2026-03-09
updated_at: 2026-03-09
tags:
    - fallback
    - architecture
seo_title: Case Study English Fallback Public Test
seo_description: This entry proves public case-study routes still fall back to English when a French translation does not exist.
client: Sidewalk Studio
role: Architecture
stack:
    - Laravel 12
outcomes:
    - English fallback remains public
---

This English-only public case study should stay reachable even when the preferred locale is French.
MD);

        try {
            $canonical = rtrim((string) config('site.url'), '/').'/en/case-studies/case-study-english-fallback-public-test';

            $this->withCookie(ResolvePublicLocale::COOKIE_NAME, 'fr')
                ->get('/fr/case-studies/case-study-english-fallback-public-test')
                ->assertOk()
                ->assertHeader('content-language', 'en')
                ->assertSee('<link rel="canonical" href="'.$canonical.'">', false)
                ->assertInertia(fn (Assert $page): Assert => $page
                    ->where('site.locale', 'en')
                    ->where('item.locale', 'en')
                    ->where('site.languageSwitcher.visible', false)
                    ->where('seo.canonical', $canonical));
        } finally {
            unlink($path);
        }
    }

    public function test_unsupported_locale_falls_back_to_english_canonical_response(): void
    {
        $canonical = rtrim((string) config('site.url'), '/').'/en/projects';

        $this->followingRedirects()->withHeaders([
            'Accept-Language' => 'de-DE,de;q=0.9',
        ])->get('/projects')
            ->assertOk()
            ->assertHeader('content-language', 'en')
            ->assertSee('<link rel="canonical" href="'.$canonical.'">', false)
            ->assertDontSee('hreflang', false)
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('hero.title', 'Ecommerce projects')
                ->where('site.locale', 'en')
                ->where('seo.openGraph.locale', 'en'));
    }
}
