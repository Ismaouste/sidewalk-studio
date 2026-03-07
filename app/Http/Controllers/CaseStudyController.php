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
        $seo = Seo::page(
            'Case Studies',
            'Detailed walkthroughs of repository bootstrap, consent orchestration, and SEO architecture decisions.',
            '/case-studies',
            [
                'breadcrumb' => [
                    ['name' => 'Home', 'path' => '/'],
                    ['name' => 'Case Studies', 'path' => '/case-studies'],
                ],
            ],
        );

        return Inertia::render('CaseStudies/Index', [
            'seo' => $seo,
            'items' => $this->content->published('case-studies')->values(),
        ])->withViewData(['seo' => $seo]);
    }

    public function show(string $slug): Response
    {
        $item = $this->content->findPublished('case-studies', $slug);
        $seo = Seo::page(
            $item['seo_title'],
            $item['seo_description'],
            '/case-studies/'.$item['slug'],
            [
                'schema_type' => 'Article',
                'open_graph_type' => 'article',
                'published_at' => $item['published_at'],
                'updated_at' => $item['updated_at'],
                'section' => 'Case Studies',
                'breadcrumb' => [
                    ['name' => 'Home', 'path' => '/'],
                    ['name' => 'Case Studies', 'path' => '/case-studies'],
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
