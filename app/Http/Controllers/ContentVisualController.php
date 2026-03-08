<?php

namespace App\Http\Controllers;

use App\Services\ContentRepository;
use App\Support\PublicLocale;
use App\Support\ContentVisual;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContentVisualController extends Controller
{
    public function __invoke(ContentRepository $content, string $section, string $slug): Response
    {
        abort_unless(in_array($section, ['writing', 'case-studies'], true), 404);

        try {
            $item = $content->findPublished($section, $slug);
        } catch (NotFoundHttpException) {
            $item = collect(PublicLocale::supported())
                ->map(fn (string $locale): ?array => $content->published($section, $locale, false)
                    ->firstWhere('slug', $slug))
                ->first(fn (?array $candidate): bool => is_array($candidate));

            abort_unless(is_array($item), 404);
        }

        $svg = ContentVisual::placeholderSvg($item);

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
