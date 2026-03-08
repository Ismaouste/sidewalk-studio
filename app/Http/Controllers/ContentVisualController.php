<?php

namespace App\Http\Controllers;

use App\Services\ContentRepository;
use App\Support\ContentVisual;
use Illuminate\Http\Response;

class ContentVisualController extends Controller
{
    public function __invoke(ContentRepository $content, string $section, string $slug): Response
    {
        abort_unless(in_array($section, ['writing', 'case-studies'], true), 404);

        $item = $content->findPublished($section, $slug);
        $svg = ContentVisual::placeholderSvg($item);

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
