<?php

namespace App\Http\Controllers;

use App\Services\ContentRepository;
use App\Services\PageContentRepository;
use App\Services\SiteSettingsService;
use App\Support\Seo;
use Inertia\Inertia;
use Inertia\Response;

class SiteController extends Controller
{
    public function __construct(
        protected ContentRepository $content,
        protected PageContentRepository $pages,
        protected SiteSettingsService $siteSettings,
    ) {}

    public function home(): Response
    {
        $settings = $this->siteSettings->current();
        $page = $this->pages->get('home');
        $seo = Seo::page(
            $page['seo_title'],
            $page['seo_description'],
            '/',
        );

        return Inertia::render('Home', [
            'seo' => $seo,
            'hero' => $page['hero'],
            'heroPanel' => $page['hero_panel'],
            'focusAreas' => $page['focus_areas'],
            'featuredCaseStudies' => $this->content->published('case-studies')->take(2)->values(),
            'latestWriting' => $this->content->published('writing')->take(2)->values(),
            'localTeaser' => $page['local_teaser'],
            'contactCta' => $page['contact_cta'],
        ])->withViewData(['seo' => $seo]);
    }

    public function experience(): Response
    {
        $page = $this->pages->get('experience');
        $seo = Seo::page(
            $page['seo_title'],
            $page['seo_description'],
            '/experience',
            [
                'breadcrumb' => [
                    ['name' => 'Home', 'path' => '/'],
                    ['name' => 'Experience', 'path' => '/experience'],
                ],
            ],
        );

        return Inertia::render('Experience', [
            'seo' => $seo,
            'hero' => $page['hero'],
            'positioning' => $page['positioning'],
            'contexts' => $page['contexts'],
            'trajectory' => $page['trajectory'],
            'strengths' => $page['strengths'],
            'focusAreas' => $page['focus_areas'],
            'stackGroups' => $page['stack_groups'],
            'lookingFor' => $page['looking_for'],
        ])->withViewData(['seo' => $seo]);
    }

    public function local(): Response
    {
        $page = $this->pages->get('local');
        $seo = Seo::page(
            $page['seo_title'],
            $page['seo_description'],
            '/local',
            [
                'breadcrumb' => [
                    ['name' => 'Home', 'path' => '/'],
                    ['name' => 'Local', 'path' => '/local'],
                ],
            ],
        );

        return Inertia::render('Local', [
            'seo' => $seo,
            'hero' => $page['hero'],
            'nancy' => $page['nancy'],
            'citySystems' => $page['city_systems'],
            'communities' => $page['communities'],
            'supportAreas' => $page['support_areas'],
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
