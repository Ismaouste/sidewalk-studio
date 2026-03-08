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
        $caseStudies = $this->content->published('case-studies')->values();
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
                    ? "Notes de dev à relire dans le flux du site."
                    : 'Development notes threaded back into the site.',
                'description' => app()->getLocale() === 'fr'
                    ? "Des notes liées au build, au contenu et à l'architecture pour prolonger la visite sans renvoyer vers une archive froide."
                    : 'Notes about build work, content systems, and architecture, meant to continue the visit without dropping people into a cold archive.',
                'ctaLabel' => app()->getLocale() === 'fr' ? 'Voir toutes les notes' : 'Browse all notes',
                'ctaHref' => '/writing',
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
            'citySystems' => $page['city_systems'],
            'communities' => $page['communities'],
            'supportAreas' => $page['support_areas'],
            'journalWidget' => $this->publicationWidget([
                'eyebrow' => app()->getLocale() === 'fr' ? 'Journal' : 'Journal',
                'title' => app()->getLocale() === 'fr'
                    ? 'Notes liées au produit, au terrain et aux systèmes.'
                    : 'Notes tied back to product, place, and systems.',
                'description' => app()->getLocale() === 'fr'
                    ? "Le journal reste ici une couche de fond reliée au reste du site, pas une section à part."
                    : 'The journal stays as a transversal support layer, not an isolated section.',
                'ctaLabel' => app()->getLocale() === 'fr' ? 'Ouvrir le journal' : 'Open the journal',
                'ctaHref' => '/writing',
                'sections' => ['writing'],
                'tag' => 'notes-dev',
                'category' => 'journal',
                'limit' => 2,
            ]),
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
            'cvDownloads' => $this->cvDownloads(),
            'tracksSection' => $page['tracks_section'],
            'caseStudies' => $caseStudies,
            'caseStudiesSection' => $page['case_studies_section'],
            'journalWidget' => $this->publicationWidget([
                'eyebrow' => app()->getLocale() === 'fr' ? 'Journal' : 'Journal',
                'title' => app()->getLocale() === 'fr'
                    ? 'Notes de dev qui prolongent les références.'
                    : 'Development notes that continue the work references.',
                'description' => app()->getLocale() === 'fr'
                    ? "Le journal reste proche des références pour rendre le raisonnement accessible, pas seulement le résultat."
                    : 'The journal stays close to the work surface so the reasoning remains reachable, not just the outcome.',
                'ctaLabel' => app()->getLocale() === 'fr' ? 'Voir le journal' : 'Open the journal',
                'ctaHref' => '/writing',
                'sections' => ['writing'],
                'tag' => 'notes-dev',
                'category' => 'journal',
                'limit' => 2,
            ]),
            'referenceWidget' => $this->publicationWidget([
                'eyebrow' => app()->getLocale() === 'fr' ? 'Références' : 'References',
                'title' => app()->getLocale() === 'fr'
                    ? 'Autres références à consulter depuis cette page.'
                    : 'More references reachable from the same work surface.',
                'description' => app()->getLocale() === 'fr'
                    ? "La distinction entre expérience et projets disparaît ici au profit d'une seule surface de travail public."
                    : 'The split between experience and projects is intentionally collapsed into one public work surface.',
                'ctaLabel' => app()->getLocale() === 'fr' ? "Voir toutes les références" : 'Browse all references',
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
                'label' => $isFrench ? 'CV anglais' : 'Download CV (EN)',
                'href' => route('career.cv.download', 'en'),
            ],
            [
                'label' => $isFrench ? 'CV français' : 'Download CV (FR)',
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
                'tag' => $options['tag'] ?? null,
                'category' => $options['category'] ?? null,
                'limit' => $options['limit'] ?? 2,
            ])->values(),
        ];
    }
}
