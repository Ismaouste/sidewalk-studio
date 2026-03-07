<?php

namespace App\Http\Controllers;

use App\Services\ContentRepository;
use App\Support\Seo;
use Inertia\Inertia;
use Inertia\Response;

class WritingController extends Controller
{
    public function __construct(
        protected ContentRepository $content,
    ) {}

    public function index(): Response
    {
        $seo = Seo::page(
            'Writing',
            'Notes on architecture, consent orchestration, content modeling, and portfolio system design.',
            '/writing',
            [
                'breadcrumb' => [
                    ['name' => 'Home', 'path' => '/'],
                    ['name' => 'Writing', 'path' => '/writing'],
                ],
            ],
        );

        return Inertia::render('Writing/Index', [
            'seo' => $seo,
            'items' => $this->content->published('writing')->values(),
        ])->withViewData(['seo' => $seo]);
    }

    public function show(string $slug): Response
    {
        $item = $this->content->findPublished('writing', $slug);
        $seo = Seo::page(
            $item['seo_title'],
            $item['seo_description'],
            '/writing/'.$item['slug'],
            [
                'schema_type' => 'BlogPosting',
                'open_graph_type' => 'article',
                'published_at' => $item['published_at'],
                'updated_at' => $item['updated_at'],
                'section' => 'Writing',
                'breadcrumb' => [
                    ['name' => 'Home', 'path' => '/'],
                    ['name' => 'Writing', 'path' => '/writing'],
                    ['name' => $item['title'], 'path' => '/writing/'.$item['slug']],
                ],
            ],
        );

        return Inertia::render('Writing/Show', [
            'seo' => $seo,
            'item' => $item,
        ])->withViewData(['seo' => $seo]);
    }
}
