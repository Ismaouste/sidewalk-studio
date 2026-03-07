<?php

namespace App\Http\Controllers;

use App\Services\ContentRepository;
use App\Services\PageContentRepository;
use App\Services\SiteSettingsService;
use App\Support\Seo;
use Illuminate\Support\Facades\File;
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
        $writing = $this->content->published('writing')->values();
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
            'latestWriting' => $writing->take(2)->values(),
            'localTeaser' => $page['local_teaser'],
            'contactCta' => $page['contact_cta'],
            'cvDownloads' => $this->cvDownloads(),
        ])->withViewData(['seo' => $seo]);
    }

    public function experience(): Response
    {
        $page = $this->pages->get('experience');
        $caseStudies = $this->content->published('case-studies')->values();
        $writing = $this->content->published('writing')->values();
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
            'careerSnapshot' => $page['career_snapshot'],
            'cvDownloads' => $this->cvDownloads(),
            'featuredCaseStudies' => $caseStudies->take(2)->values(),
            'latestWriting' => $writing->take(2)->values(),
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
        $page = $this->pages->get('projects');
        $writing = $this->content->published('writing')->values();
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
            'tracksSection' => $page['tracks_section'],
            'caseStudies' => $this->content->published('case-studies')->values(),
            'caseStudiesSection' => $page['case_studies_section'],
            'latestWriting' => $writing->take(2)->values(),
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
                'label' => $isFrench ? 'CV francais' : 'Download CV (FR)',
                'href' => route('career.cv.download', 'fr'),
            ],
        ];
    }
}
