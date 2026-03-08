<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PublicLanguageSwitcherTest extends TestCase
{
    public function test_supported_pages_expose_a_language_switcher_with_same_path_links(): void
    {
        $this->get('/projects')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('site.languageSwitcher.visible', true)
                ->where('site.languageSwitcher.current', 'en')
                ->where('site.languageSwitcher.options.0.code', 'en')
                ->where(
                    'site.languageSwitcher.options.0.href',
                    fn (string $href): bool => str_ends_with($href, '/projects?lang=en'),
                )
                ->where('site.languageSwitcher.options.1.code', 'fr')
                ->where(
                    'site.languageSwitcher.options.1.href',
                    fn (string $href): bool => str_ends_with($href, '/projects?lang=fr'),
                ));
    }

    public function test_newly_localized_contact_page_exposes_the_language_switcher(): void
    {
        $this->get('/contact')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('site.languageSwitcher.visible', true)
                ->where(
                    'site.languageSwitcher.options.1.href',
                    fn (string $href): bool => str_ends_with($href, '/contact?lang=fr'),
                ));
    }

    public function test_writing_routes_expose_the_language_switcher_when_a_french_entry_exists(): void
    {
        $this->get('/writing')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('site.languageSwitcher.visible', true)
                ->where(
                    'site.languageSwitcher.options.1.href',
                    fn (string $href): bool => str_ends_with($href, '/writing?lang=fr'),
                ));

        $this->get('/writing/content-systems-routing-and-metadata')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('site.languageSwitcher.visible', true)
                ->where(
                    'site.languageSwitcher.options.1.href',
                    fn (string $href): bool => str_ends_with($href, '/writing/content-systems-routing-and-metadata?lang=fr'),
                ));
    }

    public function test_case_study_routes_expose_the_language_switcher_when_a_french_entry_exists(): void
    {
        $this->get('/case-studies')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('site.languageSwitcher.visible', true)
                ->where(
                    'site.languageSwitcher.options.1.href',
                    fn (string $href): bool => str_ends_with($href, '/case-studies?lang=fr'),
                ));

        $this->get('/case-studies/repo-bootstrap-foundation')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('site.languageSwitcher.visible', true)
                ->where(
                    'site.languageSwitcher.options.1.href',
                    fn (string $href): bool => str_ends_with($href, '/case-studies/repo-bootstrap-foundation?lang=fr'),
                ));
    }

    public function test_unsupported_pages_hide_the_language_switcher(): void
    {
        $this->get('/labs')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('site.languageSwitcher.visible', false)
                ->where('site.languageSwitcher.options.1.available', false)
                ->where('site.languageSwitcher.options.1.href', null));
    }
}
