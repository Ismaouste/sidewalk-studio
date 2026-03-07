<?php

namespace App\Http\Controllers;

use App\Services\ContentRepository;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(ContentRepository $content): Response
    {
        $entries = collect([
            ['loc' => url('/'), 'lastmod' => now()->toDateString()],
            ['loc' => url('/experience'), 'lastmod' => now()->toDateString()],
            ['loc' => url('/local'), 'lastmod' => now()->toDateString()],
            ['loc' => url('/projects'), 'lastmod' => now()->toDateString()],
            ['loc' => url('/labs'), 'lastmod' => now()->toDateString()],
            ['loc' => url('/writing'), 'lastmod' => now()->toDateString()],
            ['loc' => url('/case-studies'), 'lastmod' => now()->toDateString()],
            ['loc' => url('/contact'), 'lastmod' => now()->toDateString()],
        ])
            ->merge($content->published('writing')->map(fn (array $item) => [
                'loc' => url('/writing/'.$item['slug']),
                'lastmod' => $item['updated_at'],
            ]))
            ->merge($content->published('case-studies')->map(fn (array $item) => [
                'loc' => url('/case-studies/'.$item['slug']),
                'lastmod' => $item['updated_at'],
            ]));

        return response()
            ->view('sitemap', ['entries' => $entries])
            ->header('Content-Type', 'application/xml');
    }
}
