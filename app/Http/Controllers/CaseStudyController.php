<?php

namespace App\Http\Controllers;

use App\Services\ContentRepository;
use App\Support\Seo;
use Inertia\Inertia;
use Inertia\Response;

class CaseStudyController extends Controller
{
    public function __construct(
        protected ContentRepository $content,
    ) {}

    public function index(): Response
    {
        $isFrench = app()->getLocale() === 'fr';
        $seo = Seo::page(
            $isFrench ? 'Cas clients' : 'Case Studies',
            $isFrench
                ? "Retours detailles sur le bootstrap du repository, l'orchestration du consentement et les choix d'architecture SEO."
                : 'Detailed walkthroughs of repository bootstrap, consent orchestration, and SEO architecture decisions.',
            '/case-studies',
            [
                'breadcrumb' => [
                    ['name' => $isFrench ? 'Accueil' : 'Home', 'path' => '/'],
                    ['name' => $isFrench ? 'Cas clients' : 'Case Studies', 'path' => '/case-studies'],
                ],
            ],
        );

        return Inertia::render('CaseStudies/Index', [
            'seo' => $seo,
            'items' => $this->content->published('case-studies', app()->getLocale(), false)->values(),
        ])->withViewData(['seo' => $seo]);
    }

    public function show(string $slug): Response
    {
        $item = $this->content->findPublished('case-studies', $slug);
        $isFrench = app()->getLocale() === 'fr';
        $seo = Seo::page(
            $item['seo_title'],
            $item['seo_description'],
            '/case-studies/'.$item['slug'],
            [
                'schema_type' => 'Article',
                'open_graph_type' => 'article',
                'published_at' => $item['published_at'],
                'updated_at' => $item['updated_at'],
                'image' => [
                    'url' => $item['image_url'],
                    'alt' => $item['image_alt'],
                ],
                'section' => $isFrench ? 'Cas clients' : 'Case Studies',
                'breadcrumb' => [
                    ['name' => $isFrench ? 'Accueil' : 'Home', 'path' => '/'],
                    ['name' => $isFrench ? 'Cas clients' : 'Case Studies', 'path' => '/case-studies'],
                    ['name' => $item['title'], 'path' => '/case-studies/'.$item['slug']],
                ],
            ],
        );

        return Inertia::render('CaseStudies/Show', [
            'seo' => $seo,
            'item' => $item,
        ])->withViewData(['seo' => $seo]);
    }
}
