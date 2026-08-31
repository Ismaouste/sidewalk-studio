<?php

namespace App\Http\Controllers;

use App\Services\ContentRepository;
use App\Support\PublicCopy;
use App\Support\PublicLocale;
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
            PublicCopy::line('seo.case_studies.title'),
            PublicCopy::line('seo.case_studies.description'),
            '/case-studies',
            [
                'robots' => 'index,follow',
                'breadcrumb' => $this->indexBreadcrumb(),
            ],
        );

        return Inertia::render('CaseStudies/Index', [
            'seo' => $seo,
            'items' => $this->content->published('case-studies', app()->getLocale(), false)->values(),
        ])->withViewData(['seo' => $seo]);
    }

    public function show(string $locale, string $slug): Response
    {
        $item = $this->content->findPublished('case-studies', $slug);
        $related = $this->content->published('case-studies', app()->getLocale(), false)
            ->reject(fn (array $candidate): bool => $candidate['slug'] === $item['slug'])
            ->take(3)
            ->values();
        $seo = Seo::page(
            $item['seo_title'],
            $item['seo_description'],
            '/case-studies/'.$item['slug'],
            [
                'schema_variant' => 'creative_work',
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
                'section' => PublicCopy::line('sections.case_studies'),
                'breadcrumb' => [
                    ...$this->indexBreadcrumb(),
                    ['name' => $item['title'], 'path' => '/case-studies/'.$item['slug']],
                ],
            ],
        );

        return Inertia::render('CaseStudies/Show', [
            'seo' => $seo,
            'item' => $item,
            'related' => $related,
        ])->withViewData(['seo' => $seo]);
    }

    /**
     * The trail down to the index, shared by both actions so a rename of a
     * segment cannot land on one of them only.
     *
     * @return array<int, array{name: string, path: string}>
     */
    protected function indexBreadcrumb(): array
    {
        return [
            ['name' => PublicLocale::homeLabel(app()->getLocale()), 'path' => '/'],
            ['name' => PublicCopy::line('breadcrumbs.projects'), 'path' => '/projects'],
            ['name' => PublicCopy::line('breadcrumbs.case_studies'), 'path' => '/case-studies'],
        ];
    }
}
