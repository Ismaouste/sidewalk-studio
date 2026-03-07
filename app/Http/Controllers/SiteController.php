<?php

namespace App\Http\Controllers;

use App\Services\ContentRepository;
use App\Services\SiteSettingsService;
use App\Support\Seo;
use Inertia\Inertia;
use Inertia\Response;

class SiteController extends Controller
{
    public function __construct(
        protected ContentRepository $content,
        protected SiteSettingsService $siteSettings,
    ) {}

    public function home(): Response
    {
        $settings = $this->siteSettings->current();
        $seo = Seo::page(
            $settings->siteIdentity->name,
            $settings->seoDefaults->defaultDescription,
            '/',
        );

        return Inertia::render('Home', [
            'seo' => $seo,
            'hero' => [
                'eyebrow' => 'Privacy-first engineering portfolio',
                'title' => 'Laravel, Inertia, structured content, and consent-aware front-end architecture.',
                'summary' => 'Sidewalk Studio is the public build log of a reusable portfolio system focused on calm UX, explicit architecture, and privacy-safe instrumentation.',
            ],
            'highlights' => [
                'Spec-driven delivery with first-class docs and roadmap discipline.',
                'Markdown content system with stable slugs and SEO metadata.',
                'Consent orchestration ready for later Matomo/PostHog integrations.',
            ],
            'featuredCaseStudies' => $this->content->published('case-studies')->take(2)->values(),
            'latestWriting' => $this->content->published('writing')->take(2)->values(),
            'labs' => config('site.labs'),
        ])->withViewData(['seo' => $seo]);
    }

    public function about(): Response
    {
        $seo = Seo::page(
            'About',
            'Context, principles, and the reason this repo exists as both portfolio and engineering reference implementation.',
            '/about',
            [
                'breadcrumb' => [
                    ['name' => 'Home', 'path' => '/'],
                    ['name' => 'About', 'path' => '/about'],
                ],
            ],
        );

        return Inertia::render('About', [
            'seo' => $seo,
            'principles' => [
                'Design public codebases that explain themselves.',
                'Treat privacy, accessibility, and SEO as architecture, not afterthoughts.',
                'Prefer reusable systems over one-off hero implementations.',
            ],
            'timeline' => [
                'Lead and stabilize complex PHP e-commerce environments.',
                'Translate production constraints into maintainable Laravel systems.',
                'Document decisions so the repo becomes a reference, not only a portfolio.',
            ],
        ])->withViewData(['seo' => $seo]);
    }

    public function projects(): Response
    {
        $seo = Seo::page(
            'Projects',
            'Selected case studies and system-building tracks that shape the first public release of Sidewalk Studio.',
            '/projects',
            [
                'breadcrumb' => [
                    ['name' => 'Home', 'path' => '/'],
                    ['name' => 'Projects', 'path' => '/projects'],
                ],
            ],
        );

        return Inertia::render('Projects', [
            'seo' => $seo,
            'caseStudies' => $this->content->published('case-studies')->values(),
            'tracks' => [
                [
                    'title' => 'Repository Foundation',
                    'summary' => 'Bootstrap a repo that can host public specs, docs, and reusable skills without collapsing into a demo-only scaffold.',
                ],
                [
                    'title' => 'Content System',
                    'summary' => 'Use versioned Markdown to publish case studies and writing with explicit metadata and stable routing.',
                ],
                [
                    'title' => 'Consent + SEO',
                    'summary' => 'Prove that privacy and discoverability can coexist in a calm, maintainable front-end architecture.',
                ],
            ],
        ])->withViewData(['seo' => $seo]);
    }

    public function labs(): Response
    {
        $seo = Seo::page(
            'Labs',
            'Sandbox areas reserved for consent, structured data, and later design-system experiments.',
            '/labs',
            [
                'breadcrumb' => [
                    ['name' => 'Home', 'path' => '/'],
                    ['name' => 'Labs', 'path' => '/labs'],
                ],
            ],
        );

        return Inertia::render('Labs', [
            'seo' => $seo,
            'labs' => config('site.labs'),
            'embedDemo' => [
                'title' => 'Consent-gated YouTube demo',
                'description' => 'This embed stays blocked until the media category is accepted. It proves the iframe orchestration path without loading analytics by default.',
                'service' => 'youtube',
                'id' => 'dQw4w9WgXcQ',
            ],
        ])->withViewData(['seo' => $seo]);
    }

    public function contact(): Response
    {
        $settings = $this->siteSettings->current();
        $seo = Seo::page(
            'Contact',
            'Preferred collaboration channels for privacy, SEO, and Laravel modernization work.',
            '/contact',
            [
                'breadcrumb' => [
                    ['name' => 'Home', 'path' => '/'],
                    ['name' => 'Contact', 'path' => '/contact'],
                ],
            ],
        );

        return Inertia::render('Contact', [
            'seo' => $seo,
            'contact' => $settings->contactDetails->toArray(),
            'services' => [
                'Platform stabilization and legacy recovery.',
                'Consent-safe analytics and embed architecture.',
                'SEO and content-model foundations for editorial sites.',
            ],
        ])->withViewData(['seo' => $seo]);
    }
}
