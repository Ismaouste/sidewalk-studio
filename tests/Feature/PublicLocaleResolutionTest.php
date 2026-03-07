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
            ->get('/writing')
            ->assertOk()
            ->assertHeader('content-language', 'en')
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('site.locale', 'en')
                ->where('site.languageSwitcher.visible', false));
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
