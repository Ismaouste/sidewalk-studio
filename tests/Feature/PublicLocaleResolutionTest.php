<?php

namespace Tests\Feature;

use App\Http\Middleware\ResolvePublicLocale;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PublicLocaleResolutionTest extends TestCase
{
    public function test_browser_language_prefers_french_page_content(): void
    {
        $canonical = rtrim((string) config('site.url'), '/').'/experience';
        $response = $this->withHeaders([
            'Accept-Language' => 'fr-FR,fr;q=0.9,en;q=0.8',
        ])->get('/experience');

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
                ->where('hero.title', 'Un parcours faconne par les systemes complexes.')
                ->where('site.locale', 'fr')
                ->where('seo.canonical', $canonical)
                ->where('seo.openGraph.locale', 'fr'));
    }

    public function test_explicit_lang_query_persists_locale_preference(): void
    {
        $this->get('/local?lang=fr')
            ->assertOk()
            ->assertCookie(ResolvePublicLocale::COOKIE_NAME, 'fr')
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('hero.eyebrow', 'Ancrage local')
                ->where('site.locale', 'fr')
                ->where('site.languageSwitcher.visible', true));

        $this->withCookie(ResolvePublicLocale::COOKIE_NAME, 'fr')
            ->withHeaders([
                'Accept-Language' => 'en-US,en;q=0.9',
            ])->get('/')
            ->assertOk()
            ->assertHeader('content-language', 'fr')
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('hero.eyebrow', "Pratique d'ingenierie calme")
                ->where('site.locale', 'fr'));
    }

    public function test_newly_localized_contact_page_renders_french_content(): void
    {
        $this->withCookie(ResolvePublicLocale::COOKIE_NAME, 'fr')
            ->get('/contact')
            ->assertOk()
            ->assertHeader('content-language', 'fr')
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('site.locale', 'fr')
                ->where('hero.title', 'Conversations autour de la vie privee, du SEO et de la modernisation Laravel.')
                ->where('site.languageSwitcher.visible', true));
    }

    public function test_pages_without_french_source_stay_on_english_even_with_french_preference(): void
    {
        $this->withCookie(ResolvePublicLocale::COOKIE_NAME, 'fr')
            ->get('/labs')
            ->assertOk()
            ->assertHeader('content-language', 'en')
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('site.locale', 'en')
                ->where('site.languageSwitcher.visible', false));
    }

    public function test_writing_index_renders_french_entries_when_french_locale_is_resolved(): void
    {
        $canonical = rtrim((string) config('site.url'), '/').'/writing';

        $this->withCookie(ResolvePublicLocale::COOKIE_NAME, 'fr')
            ->get('/writing')
            ->assertOk()
            ->assertHeader('content-language', 'fr')
            ->assertSee('<link rel="canonical" href="'.$canonical.'">', false)
            ->assertDontSee('hreflang', false)
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('site.locale', 'fr')
                ->where('items.0.locale', 'fr')
                ->where('items.0.title', 'Pourquoi le SSR reste pret mais differe')
                ->where('site.languageSwitcher.visible', true)
                ->where('seo.canonical', $canonical));
    }

    public function test_writing_detail_renders_localized_french_entry_with_stable_canonical_url(): void
    {
        $canonical = rtrim((string) config('site.url'), '/').'/writing/content-systems-routing-and-metadata';

        $this->withCookie(ResolvePublicLocale::COOKIE_NAME, 'fr')
            ->get('/writing/content-systems-routing-and-metadata')
            ->assertOk()
            ->assertHeader('content-language', 'fr')
            ->assertSee('<link rel="canonical" href="'.$canonical.'">', false)
            ->assertDontSee('hreflang', false)
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('site.locale', 'fr')
                ->where('item.locale', 'fr')
                ->where('item.title', 'Les systemes de contenu commencent par le routage et les metadonnees')
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
            $canonical = rtrim((string) config('site.url'), '/').'/writing/editorial-english-fallback-public-test';

            $this->withCookie(ResolvePublicLocale::COOKIE_NAME, 'fr')
                ->get('/writing/editorial-english-fallback-public-test')
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
        $canonical = rtrim((string) config('site.url'), '/').'/case-studies';

        $this->withCookie(ResolvePublicLocale::COOKIE_NAME, 'fr')
            ->get('/case-studies')
            ->assertOk()
            ->assertHeader('content-language', 'fr')
            ->assertSee('<link rel="canonical" href="'.$canonical.'">', false)
            ->assertDontSee('hreflang', false)
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('site.locale', 'fr')
                ->where('items.0.locale', 'fr')
                ->where('items.0.title', 'Orchestration du consentement avant les analytics')
                ->where('site.languageSwitcher.visible', true)
                ->where('seo.canonical', $canonical));
    }

    public function test_case_study_detail_renders_localized_french_entry_with_stable_canonical_url(): void
    {
        $canonical = rtrim((string) config('site.url'), '/').'/case-studies/repo-bootstrap-foundation';

        $this->withCookie(ResolvePublicLocale::COOKIE_NAME, 'fr')
            ->get('/case-studies/repo-bootstrap-foundation')
            ->assertOk()
            ->assertHeader('content-language', 'fr')
            ->assertSee('<link rel="canonical" href="'.$canonical.'">', false)
            ->assertDontSee('hreflang', false)
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('site.locale', 'fr')
                ->where('item.locale', 'fr')
                ->where('item.title', 'Bootstrap du repository pour un portfolio pilote par les specs')
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
            $canonical = rtrim((string) config('site.url'), '/').'/case-studies/case-study-english-fallback-public-test';

            $this->withCookie(ResolvePublicLocale::COOKIE_NAME, 'fr')
                ->get('/case-studies/case-study-english-fallback-public-test')
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
        $canonical = rtrim((string) config('site.url'), '/').'/experience';

        $this->withHeaders([
            'Accept-Language' => 'de-DE,de;q=0.9',
        ])->get('/experience')
            ->assertOk()
            ->assertHeader('content-language', 'en')
            ->assertSee('<link rel="canonical" href="'.$canonical.'">', false)
            ->assertDontSee('hreflang', false)
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('hero.title', 'Laravel, e-commerce, technical SEO, and privacy-aware product systems.')
                ->where('site.locale', 'en')
                ->where('seo.openGraph.locale', 'en'));
    }
}
