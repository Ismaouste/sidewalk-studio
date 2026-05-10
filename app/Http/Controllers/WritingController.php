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
            $isFrench ? 'Blog technique e-commerce PHP' : 'Journal',
            $isFrench
                ? "Blog technique sur l'e-commerce PHP, Laravel, l'orchestration du consentement, la modélisation de contenu et les détails qui comptent vraiment en production."
                : 'Technical notes on ecommerce PHP, Laravel, consent orchestration, content modeling, and the details that matter in production.',
            '/journal',
            [
                'robots' => 'index,follow',
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

    public function show(string $locale, string $slug): Response
    {
        $item = $this->content->findPublished('writing', $slug);
        $isFrench = app()->getLocale() === 'fr';
        $related = $this->content->published('writing', app()->getLocale(), false)
            ->reject(fn (array $candidate): bool => $candidate['slug'] === $item['slug'])
            ->take(3)
            ->values();
        $seo = Seo::page(
            $item['seo_title'],
            $item['seo_description'],
            '/journal/'.$item['slug'],
            [
                'schema_variant' => 'article',
                'published_at' => $item['published_at'],
                'updated_at' => $item['updated_at'],
                'robots' => $item['robots'],
                'canonical_url' => $item['canonical_url'],
                'keywords' => $item['tags'],
                'image' => [
                    'url' => $item['open_graph_image'],
                    'alt' => $item['featured_image_alt'] ?: $item['image_alt'],
                    'slug' => $item['slug'],
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
            'related' => $related,
        ])->withViewData(['seo' => $seo]);
    }
}
