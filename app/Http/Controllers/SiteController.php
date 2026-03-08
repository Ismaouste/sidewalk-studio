<?php

namespace App\Http\Controllers;

use App\Services\ContentRepository;
use App\Services\PageContentRepository;
use App\Services\SiteSettingsService;
use App\Support\Seo;
use Illuminate\Support\Facades\File;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
        $caseStudies = $this->content->published('case-studies', app()->getLocale(), false)->values();
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
            'featuredCaseStudies' => $caseStudies->take(2)->values(),
            'journalWidget' => $this->publicationWidget([
                'eyebrow' => app()->getLocale() === 'fr' ? 'Journal' : 'Journal',
                'title' => app()->getLocale() === 'fr'
                    ? 'Articles, notes et détails techniques qui valent le détour.'
                    : 'Articles, notes, and technical details worth opening.',
                'description' => app()->getLocale() === 'fr'
                    ? "Des textes plus construits et des mémos plus courts pour parler terrain, flux produit, SEO, données structurées, outils associatifs et détails de build."
                    : 'Longer articles and shorter memos about product flows, structured data, SEO, nonprofit tooling, and build details.',
                'ctaLabel' => app()->getLocale() === 'fr' ? 'Découvrir le journal' : 'Discover the journal',
                'ctaHref' => '/journal',
                'sections' => ['writing'],
                'tag' => 'notes-dev',
                'category' => 'journal',
                'limit' => 2,
            ]),
            'localTeaser' => $page['local_teaser'],
            'contactCta' => $page['contact_cta'],
            'cvDownloads' => $this->cvDownloads(),
        ])->withViewData(['seo' => $seo]);
    }

    public function experience(): RedirectResponse
    {
        return redirect('/projects', 301);
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
            'signals' => $page['signals'],
            'journalSection' => $page['journal_section'],
            'journalItems' => $this->content->feed(['writing'], [
                'tag' => 'notes-dev',
                'category' => 'journal',
                'limit' => 3,
            ])->values(),
            'engagementsIntro' => $page['engagements_intro'],
            'engagements' => $page['engagements'],
            'notesSection' => $page['notes_section'],
            'notes' => $this->content->feed(['writing'], [
                'tag' => 'notes-dev',
            ])->values(),
        ])->withViewData(['seo' => $seo]);
    }

    public function projects(): Response
    {
        $page = $this->pages->get('projects');
        $experience = $this->pages->get('experience');
        $caseStudies = $this->content->feed(['case-studies'], [
            'category' => 'work',
        ])->values();
        $seo = Seo::page(
            $page['seo_title'],
            $page['seo_description'],
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
            'hero' => $page['hero'],
            'positioning' => $experience['positioning'],
            'contexts' => $experience['contexts'],
            'stackGroups' => $experience['stack_groups'],
            'careerSnapshot' => $experience['career_snapshot'],
            'lookingFor' => $experience['looking_for'],
            'professionalSections' => $experience['professional_sections'],
            'associativeSections' => $experience['associative_sections'],
            'sideProjectSections' => $experience['side_project_sections'],
            'cvDownloads' => $this->cvDownloads(),
            'tracksSection' => $page['tracks_section'],
            'caseStudies' => $caseStudies,
            'caseStudiesSection' => $page['case_studies_section'],
            'associativeNoteWidget' => [
                'eyebrow' => $experience['associative_note_widget']['eyebrow'],
                'title' => $experience['associative_note_widget']['title'],
                'description' => $experience['associative_note_widget']['description'],
                'ctaLabel' => $experience['associative_note_widget']['cta_label'],
                'ctaHref' => '',
                'items' => [
                    $this->localizedWriting(
                        'opensurvey-associatif-donnees-sante',
                        'opensurvey-nonprofit-health-data',
                    ),
                ],
            ],
            'sideProjectsWidget' => [
                'eyebrow' => $experience['side_projects_widget']['eyebrow'],
                'title' => $experience['side_projects_widget']['title'],
                'description' => $experience['side_projects_widget']['description'],
                'ctaLabel' => $experience['side_projects_widget']['cta_label'],
                'ctaHref' => '/journal',
                'items' => [
                    $this->localizedWriting(
                        'volontariat-njp-et-petits-outils',
                        'njp-volunteering-and-small-tools',
                    ),
                    $this->localizedWriting(
                        'ytmusic-liked-sorter',
                        'ytmusic-liked-sorter',
                    ),
                ],
            ],
            'journalWidget' => $this->publicationWidget([
                'eyebrow' => app()->getLocale() === 'fr' ? 'Notes' : 'Notes',
                'title' => app()->getLocale() === 'fr'
                    ? 'Mémos techniques, anecdotes utiles, détails qui comptent.'
                    : 'Technical memos, useful anecdotes, and details that matter.',
                'description' => app()->getLocale() === 'fr'
                    ? "Des notes courtes pour parler schéma.org, données produit, catalogues, images, formats web et autres détails techniques qui finissent par faire une vraie différence."
                    : 'Short notes about schema.org, product data, catalogs, images, web formats, and the technical details that end up making a real difference.',
                'ctaLabel' => app()->getLocale() === 'fr' ? 'Consulter les notes' : 'Browse notes',
                'ctaHref' => '/journal',
                'sections' => ['writing'],
                'tag' => 'notes-dev',
                'publication_type' => 'note',
                'limit' => 2,
            ]),
            'referenceWidget' => $this->publicationWidget([
                'eyebrow' => app()->getLocale() === 'fr' ? 'Références' : 'References',
                'title' => app()->getLocale() === 'fr'
                    ? 'Études de cas et notes pour aller plus loin.'
                    : 'Case studies and notes to go deeper.',
                'description' => app()->getLocale() === 'fr'
                    ? "Une archive pour entrer dans des cas plus précis : outils associatifs sous contrainte, circulation de la donnée entre ERP, PIM et e-commerce, formats web, sitemaps, robots.txt et qualité de livraison."
                    : 'An archive for more precise cases: constrained nonprofit tooling, data flow between ERP, PIM, and commerce, web formats, sitemaps, robots.txt, and delivery quality.',
                'ctaLabel' => app()->getLocale() === 'fr' ? "Découvrir toutes les études de cas" : 'Browse all case studies',
                'ctaHref' => '/case-studies',
                'sections' => ['case-studies'],
                'category' => 'work',
                'limit' => 2,
            ]),
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
        $page = $this->pages->get('contact');
        $seo = Seo::page(
            $page['seo_title'],
            $page['seo_description'],
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
            'hero' => $page['hero'],
            'form' => $page['form'],
            'details' => $page['details'],
            'cvDownloads' => $this->cvDownloads(),
            'services' => $page['services'],
            'recruiterShortcut' => $page['recruiter_shortcut'],
        ])->withViewData(['seo' => $seo]);
    }

    public function dataProcessing(): Response
    {
        $page = $this->pages->get('data-processing');
        $seo = Seo::page(
            $page['seo_title'],
            $page['seo_description'],
            '/data-processing',
            [
                'breadcrumb' => [
                    ['name' => 'Home', 'path' => '/'],
                    ['name' => 'Data processing', 'path' => '/data-processing'],
                ],
                'robots' => 'noindex,nofollow',
            ],
        );

        return Inertia::render('DataProcessing', [
            'seo' => $seo,
            'hero' => $page['hero'],
            'storage' => $page['storage'],
            'consent' => $page['consent'],
            'operator' => $page['operator'],
        ])->withViewData(['seo' => $seo]);
    }

    public function downloadCv(string $locale): BinaryFileResponse
    {
        abort_unless(in_array($locale, ['en', 'fr'], true), 404);

        $path = base_path("docs/career/output/ismael-rodmacq-cv-{$locale}.pdf");

        abort_unless(File::exists($path), 404);

        return response()->download(
            $path,
            "ismael-rodmacq-cv-{$locale}.pdf",
            [
                'Content-Type' => 'application/pdf',
                'X-Robots-Tag' => 'noindex, nofollow',
            ],
        );
    }

    /**
     * @return array<int, array{label: string, href: string}>
     */
    protected function cvDownloads(): array
    {
        $isFrench = app()->getLocale() === 'fr';

        return [
            [
                'label' => 'CV EN / PDF',
                'href' => route('career.cv.download', 'en'),
            ],
            [
                'label' => 'CV FR / PDF',
                'href' => route('career.cv.download', 'fr'),
            ],
        ];
    }

    /**
     * @param  array{
     *   eyebrow: string,
     *   title: string,
     *   description: string,
     *   ctaLabel: string,
     *   ctaHref: string,
     *   sections: array<int, string>,
     *   tag?: string,
     *   category?: string,
     *   publication_type?: string,
     *   limit?: int
     * }  $options
     * @return array<string, mixed>
     */
    protected function publicationWidget(array $options): array
    {
        return [
            'eyebrow' => $options['eyebrow'],
            'title' => $options['title'],
            'description' => $options['description'],
            'ctaLabel' => $options['ctaLabel'],
            'ctaHref' => $options['ctaHref'],
            'items' => $this->content->feed($options['sections'], [
                'locale' => app()->getLocale(),
                'include_fallback' => false,
                'tag' => $options['tag'] ?? null,
                'category' => $options['category'] ?? null,
                'publication_type' => $options['publication_type'] ?? null,
                'limit' => $options['limit'] ?? 2,
            ])->values(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function localizedWriting(string $frenchSlug, string $englishSlug): array
    {
        return $this->content->findPublished(
            'writing',
            app()->getLocale() === 'fr' ? $frenchSlug : $englishSlug,
        );
    }
}
