<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CareerDownloadsTest extends TestCase
{
    public function test_cv_download_routes_are_publicly_available(): void
    {
        $this->get('/cv/en')
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeader('x-robots-tag', 'noindex, nofollow');

        $this->get('/cv/fr')
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeader('x-robots-tag', 'noindex, nofollow');
    }

    public function test_home_projects_and_contact_pages_expose_cv_download_links(): void
    {
        $this->get('/en')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('cvDownloads.0.href', fn (string $href): bool => str_ends_with($href, '/cv/en'))
                ->where('cvDownloads.1.href', fn (string $href): bool => str_ends_with($href, '/cv/fr')));

        $this->get('/en/projects')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('cvDownloads.0.href', fn (string $href): bool => str_ends_with($href, '/cv/en'))
                ->where('cvDownloads.1.href', fn (string $href): bool => str_ends_with($href, '/cv/fr'))
                ->where(
                    'careerSnapshot.title',
                    'Recruiter-ready snapshot',
                ));

        $this->get('/en/contact')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('cvDownloads.0.href', fn (string $href): bool => str_ends_with($href, '/cv/en'))
                ->where('cvDownloads.1.href', fn (string $href): bool => str_ends_with($href, '/cv/fr')));
    }

    public function test_home_and_projects_pages_expose_public_navigation_widgets(): void
    {
        $this->get('/en')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->has('featuredCaseStudies', 2)
                ->has('journalWidget.items', 2));

        $this->get('/en/projects')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->has('journalWidget.items', 2)
                ->has('referenceWidget.items', 2)
                ->has('caseStudies', 2));
    }
}
