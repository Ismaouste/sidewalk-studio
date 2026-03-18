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
        $isFrench = app()->getLocale() === 'fr';
        $seo = Seo::page(
            'Journal',
            $isFrench
                ? "Notes sur l'architecture, l'orchestration du consentement, la modelisation de contenu et la conception d'un portfolio systeme."
                : 'Notes on architecture, consent orchestration, content modeling, and portfolio system design.',
            '/journal',
            [
                'breadcrumb' => [
                    ['name' => $isFrench ? 'Accueil' : 'Home', 'path' => '/'],
                    ['name' => 'Journal', 'path' => '/journal'],
                ],
            ],
        );

        return Inertia::render('Writing/Index', [
            'seo' => $seo,
            'items' => $this->content->published('writing', app()->getLocale(), false)->values(),
        ])->withViewData(['seo' => $seo]);
    }

    public function show(string $slug): Response
    {
        $item = $this->content->findPublished('writing', $slug);
        $isFrench = app()->getLocale() === 'fr';
        $seo = Seo::page(
            $item['seo_title'],
            $item['seo_description'],
            '/journal/'.$item['slug'],
            [
                'schema_type' => 'BlogPosting',
                'open_graph_type' => 'article',
                'published_at' => $item['published_at'],
                'updated_at' => $item['updated_at'],
                'image' => [
                    'url' => $item['image_url'],
                    'alt' => $item['image_alt'],
                ],
                'section' => 'Journal',
                'breadcrumb' => [
                    ['name' => $isFrench ? 'Accueil' : 'Home', 'path' => '/'],
                    ['name' => 'Journal', 'path' => '/journal'],
                    ['name' => $item['title'], 'path' => '/journal/'.$item['slug']],
                ],
            ],
        );

        return Inertia::render('Writing/Show', [
            'seo' => $seo,
            'item' => $item,
        ])->withViewData(['seo' => $seo]);
    }
}
