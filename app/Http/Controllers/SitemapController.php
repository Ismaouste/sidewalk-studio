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
                'alternates' => [],
            ]))
            ->merge($content->published('writing', 'fr', false)->map(fn (array $item) => [
                'loc' => url($item['url']),
                'lastmod' => $item['updated_at'],
                'alternates' => [],
            ]))
            ->merge($content->published('case-studies', 'en', false)->map(fn (array $item) => [
                'loc' => url($item['url']),
                'lastmod' => $item['updated_at'],
                'alternates' => [],
            ]))
            ->merge($content->published('case-studies', 'fr', false)->map(fn (array $item) => [
                'loc' => url($item['url']),
                'lastmod' => $item['updated_at'],
                'alternates' => [],
            ]))
            ->unique('loc')
            ->values();

        return response()
            ->view('sitemap', ['entries' => $entries])
            ->header('Content-Type', 'application/xml');
    }

    /**
     * @return array<int, array{loc: string, lastmod: string, alternates: array<int, array{hreflang: string, href: string}>}>
     */
    protected function staticPageEntries(): array
    {
        $paths = ['/', '/local', '/projects', '/contact', '/journal', '/case-studies'];

        return collect(PublicLocale::supported())
            ->crossJoin($paths)
            ->map(fn (array $pair): array => [
                'locale' => $pair[0],
                'path' => $pair[1],
            ])
            ->map(fn (array $entry): array => [
                'loc' => url(PublicLocale::localizedPath($entry['path'], $entry['locale'])),
                'lastmod' => now()->toDateString(),
                'alternates' => $this->alternatesFor($entry['path']),
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{hreflang: string, href: string}>
     */
    protected function alternatesFor(string $path): array
    {
        $alternates = collect(PublicLocale::supported())
            ->map(fn (string $locale): array => [
                'hreflang' => $locale,
                'href' => url(PublicLocale::localizedPath($path, $locale)),
            ])
            ->all();

        $alternates[] = [
            'hreflang' => 'x-default',
            'href' => url(PublicLocale::localizedPath($path, PublicLocale::default())),
        ];

        return $alternates;
    }
}
