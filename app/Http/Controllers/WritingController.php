<?php

namespace App\Http\Controllers;

use App\Services\ContentRepository;
use App\Support\PublicCopy;
use App\Support\PublicLocale;
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
            PublicCopy::line('seo.journal.title'),
            PublicCopy::line('seo.journal.description'),
            '/journal',
            [
                'robots' => 'index,follow',
                'breadcrumb' => [
                    ['name' => PublicLocale::homeLabel(app()->getLocale()), 'path' => '/'],
                    ['name' => PublicCopy::line('breadcrumbs.journal'), 'path' => '/journal'],
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
                'section' => PublicCopy::line('sections.journal'),
                'breadcrumb' => [
                    ['name' => PublicLocale::homeLabel(app()->getLocale()), 'path' => '/'],
                    ['name' => PublicCopy::line('breadcrumbs.journal'), 'path' => '/journal'],
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
