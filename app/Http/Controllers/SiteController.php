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
                'eyebrow' => 'Calm engineering practice',
                'title' => 'Calm engineering for e-commerce, privacy, and durable products.',
                'summary' => 'I work on maintainability, technical SEO, consent-aware analytics, readable product architecture, and complex Laravel environments that need to stay useful over time.',
            ],
            'heroPanel' => [
                'Complex e-commerce and product systems with real legacy weight.',
                'Technical SEO, privacy, and maintainability handled inside delivery, not as parallel checklists.',
                'A practice grounded in Nancy and attentive to civic interfaces, service clarity, and public context.',
            ],
            'focusAreas' => [
                [
                    'label' => 'Experience',
                    'title' => 'Stabilize complex e-commerce systems',
                    'summary' => 'Take over Laravel or PHP environments with history, reduce operational risk, and improve delivery confidence without pretending the legacy does not exist.',
                    'href' => '/experience',
                    'cta' => 'Read experience',
                    'tone' => 'dominant',
                ],
                [
                    'label' => 'Projects',
                    'title' => 'Make architecture legible in public',
                    'summary' => 'Use case studies and structured proof to show how technical SEO, privacy, and maintainability decisions fit together in real product systems.',
                    'href' => '/projects',
                    'cta' => 'View projects',
                    'tone' => 'green',
                ],
                [
                    'label' => 'Local',
                    'title' => 'Keep product work connected to place',
                    'summary' => 'Stay attentive to cities, public space, local information systems, and community contexts so interfaces remain readable for actual people.',
                    'href' => '/local',
                    'cta' => 'Read local perspective',
                    'tone' => 'coral',
                ],
            ],
            'featuredCaseStudies' => $this->content->published('case-studies')->take(2)->values(),
            'latestWriting' => $this->content->published('writing')->take(2)->values(),
            'localTeaser' => [
                'title' => 'What I care about in cities and on the web',
                'summary' => 'Nancy is a real base, not a postcard. The work is informed by public space, transport logic, local information systems, associative life, and the question of how services stay readable.',
                'points' => [
                    'Public information design and urban interfaces.',
                    'Local APIs, mapping, transport logic, and service clarity.',
                    'Associative and cultural communities, including Aremedia.',
                ],
            ],
            'contactCta' => [
                'title' => 'Open to careful work in difficult environments.',
                'summary' => 'If a product, commerce platform, or editorial system needs calmer engineering and clearer architecture, I am happy to talk.',
            ],
        ])->withViewData(['seo' => $seo]);
    }

    public function experience(): Response
    {
        $seo = Seo::page(
            'Experience',
            'Professional trajectory across e-commerce, Laravel, technical SEO, privacy, maintainability, and legacy modernization.',
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
            'hero' => [
                'eyebrow' => 'Experience',
                'title' => 'Laravel, e-commerce, technical SEO, and privacy-aware product systems.',
                'summary' => 'I work where delivery pressure, maintainability, legacy modernization, and public experience all have to coexist without drama.',
            ],
            'positioning' => [
                'I am most useful on established products and commerce platforms that already matter to a business and need calmer engineering rather than a theatrical reset.',
                'The role usually combines implementation, architecture, and translation between product constraints, technical SEO, privacy, consent-aware analytics, and long-term maintainability.',
            ],
            'contexts' => [
                'Revenue-critical e-commerce stacks with real operational memory.',
                'Laravel and PHP systems that need incremental legacy modernization.',
                'Editorial and marketing surfaces where routing, metadata, and content structure are product concerns.',
            ],
            'trajectory' => [
                [
                    'title' => 'Commerce platforms under pressure',
                    'summary' => 'Worked in environments where releases, checkout flows, search visibility, and operational stability were tightly coupled. That is where I learned to prefer careful system design over abstract purity.',
                ],
                [
                    'title' => 'Laravel as a durable delivery layer',
                    'summary' => 'Used Laravel to bring clearer boundaries, safer refactors, and more readable internal conventions to products that had already accumulated business-critical complexity.',
                ],
                [
                    'title' => 'Documentation as part of implementation',
                    'summary' => 'Turned architectural decisions into public-facing specs, notes, and internal references so maintainability would survive team changes and future delivery rounds.',
                ],
            ],
            'strengths' => [
                'Map a messy legacy surface into a smaller, clearer architecture.',
                'Connect product, technical SEO, privacy, and engineering concerns without splitting them into separate silos.',
                'Write code and documentation that make future maintenance cheaper.',
            ],
            'focusAreas' => [
                [
                    'title' => 'Laravel and legacy modernization',
                    'summary' => 'Incremental refactors, better boundaries, and practical conventions for long-lived PHP systems that cannot stop shipping.',
                ],
                [
                    'title' => 'Technical SEO and information architecture',
                    'summary' => 'Routing, metadata, canonical logic, sitemap discipline, and public content structures that remain coherent after launch.',
                ],
                [
                    'title' => 'Privacy and consent-aware analytics',
                    'summary' => 'Consent layers, embed gating, event design, and analytics decisions that do not weaken trust or blur system boundaries.',
                ],
            ],
            'stackGroups' => [
                [
                    'title' => 'Core stack',
                    'items' => ['Laravel', 'PHP', 'Inertia.js', 'Vue', 'SQL', 'XML and JSON-LD'],
                ],
                [
                    'title' => 'Typical environments',
                    'items' => ['E-commerce platforms', 'Content-rich public sites', 'Legacy monoliths with active delivery pressure'],
                ],
                [
                    'title' => 'Working methods',
                    'items' => ['Spec-driven planning', 'Incremental delivery', 'Documentation alongside code', 'Privacy and SEO review inside implementation'],
                ],
            ],
            'lookingFor' => 'I am currently most interested in product, commerce, or editorial systems that already exist, already matter, and need steadier engineering, clearer architecture, and a more durable technical foundation.',
        ])->withViewData(['seo' => $seo]);
    }

    public function local(): Response
    {
        $seo = Seo::page(
            'Local',
            'Editorial context about Nancy, civic systems, local information design, supported communities, and the place from which the work is done.',
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
            'hero' => [
                'eyebrow' => 'Local ground',
                'title' => 'Work grounded in place, public space, and readable local systems.',
                'summary' => 'Nancy is not a backdrop. It is the civic and cultural base from which I think about service design, urban interfaces, community life, and the everyday quality of digital systems.',
            ],
            'nancy' => [
                'body' => [
                    'Nancy is my real base. That matters because it keeps the work close to actual streets, transport habits, cultural institutions, associations, and service friction rather than an abstract idea of users.',
                    'I pay attention to how people move through a city, how information is exposed, and how small interface decisions affect trust, orientation, and access.',
                ],
                'signals' => [
                    'Street-level public space and service legibility.',
                    'Regional mobility, transport logic, and local information flows.',
                    'Cultural and associative ecosystems that keep knowledge close to the city.',
                ],
            ],
            'citySystems' => [
                [
                    'title' => 'Urban interfaces',
                    'summary' => 'Wayfinding, timetables, booking flows, maps, and service pages are product systems too. They deserve the same clarity and maintainability as any commerce surface.',
                ],
                [
                    'title' => 'Local data and APIs',
                    'summary' => 'I am curious about transport data, mapping logic, local APIs, and the small technical contracts that make civic information reusable instead of opaque.',
                ],
                [
                    'title' => 'Readable service systems',
                    'summary' => 'The same discipline that matters on the web matters in cities: clear affordances, understandable states, and interfaces that respect people’s attention.',
                ],
            ],
            'communities' => [
                [
                    'title' => 'Aremedia',
                    'summary' => 'I support Aremedia as part of a wider interest in accessible cultural work, community-oriented media, and durable local knowledge networks.',
                ],
                [
                    'title' => 'Nancy associations and local initiatives',
                    'summary' => 'I pay attention to projects that keep culture, mutual support, and public usefulness close to the city rather than abstracting them away.',
                ],
                [
                    'title' => 'Paris-linked communities',
                    'summary' => 'Some conversations extend to Paris-linked associations and communities around documentation, civic tech, and public-interest digital services, without breaking the local anchor.',
                ],
            ],
            'supportAreas' => [
                [
                    'title' => 'Projects I support',
                    'summary' => 'The projects I gravitate toward tend to make information clearer, community work more visible, or local services easier to navigate.',
                    'items' => [
                        'Community-oriented media and cultural initiatives.',
                        'Local public-interest digital services.',
                        'Readable editorial and informational systems for small organizations.',
                    ],
                ],
                [
                    'title' => 'Open-source curiosity',
                    'summary' => 'I follow tools that help map, document, and expose public information in a way that stays inspectable and reusable.',
                    'items' => [
                        'Mapping and transport-adjacent tooling.',
                        'Open data and public information publishing workflows.',
                        'Documentation-heavy tools that make service systems easier to understand.',
                    ],
                ],
                [
                    'title' => 'Civic questions',
                    'summary' => 'The recurring question is simple: how do interfaces stay legible when budgets, legacy systems, and institutional constraints are all real?',
                    'items' => [
                        'How local APIs and public datasets are surfaced.',
                        'How transport and service logic are explained rather than hidden.',
                        'How digital public space can remain calm, useful, and culturally aware.',
                    ],
                ],
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
