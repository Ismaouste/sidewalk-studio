<?php

namespace App\Http\Controllers;

use App\Services\ContentRepository;
use App\Support\PublicLocale;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(ContentRepository $content): Response
    {
        $entries = collect($this->staticPageEntries())
            ->merge($content->published('writing', 'en', false)->map(fn (array $item) => [
                'loc' => url($item['url']),
                'lastmod' => $item['updated_at'],
            ]))
            ->merge($content->published('writing', 'fr', false)->map(fn (array $item) => [
                'loc' => url($item['url']),
                'lastmod' => $item['updated_at'],
            ]))
            ->merge($content->published('case-studies', 'en', false)->map(fn (array $item) => [
                'loc' => url($item['url']),
                'lastmod' => $item['updated_at'],
            ]))
            ->merge($content->published('case-studies', 'fr', false)->map(fn (array $item) => [
                'loc' => url($item['url']),
                'lastmod' => $item['updated_at'],
            ]))
            ->unique('loc')
            ->values();

        return response()
            ->view('sitemap', ['entries' => $entries])
            ->header('Content-Type', 'application/xml');
    }

    /**
     * @return array<int, array{loc: string, lastmod: string}>
     */
    protected function staticPageEntries(): array
    {
        return collect([
            ['locale' => 'en', 'path' => '/'],
            ['locale' => 'en', 'path' => '/local'],
            ['locale' => 'en', 'path' => '/experience'],
            ['locale' => 'en', 'path' => '/contact'],
            ['locale' => 'fr', 'path' => '/'],
            ['locale' => 'fr', 'path' => '/local'],
            ['locale' => 'fr', 'path' => '/experience'],
            ['locale' => 'fr', 'path' => '/contact'],
        ])
            ->map(fn (array $entry): array => [
                'loc' => url(PublicLocale::localizedPath($entry['path'], $entry['locale'])),
                'lastmod' => now()->toDateString(),
            ])
            ->merge([
                [
                    'loc' => url(PublicLocale::localizedPath('/journal', 'en')),
                    'lastmod' => now()->toDateString(),
                ],
                [
                    'loc' => url(PublicLocale::localizedPath('/journal', 'fr')),
                    'lastmod' => now()->toDateString(),
                ],
                [
                    'loc' => url(PublicLocale::localizedPath('/case-studies', 'en')),
                    'lastmod' => now()->toDateString(),
                ],
                [
                    'loc' => url(PublicLocale::localizedPath('/case-studies', 'fr')),
                    'lastmod' => now()->toDateString(),
                ],
            ])
            ->values()
            ->all();
    }
}
