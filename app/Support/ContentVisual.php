<?php

namespace App\Support;

use Illuminate\Support\Str;

class ContentVisual
{
    /**
     * @param  array<string, mixed>  $item
     */
    public static function tone(array $item): string
    {
        if (! empty($item['accent_tone'])) {
            return (string) $item['accent_tone'];
        }

        if (($item['section'] ?? 'writing') === 'writing') {
            return 'violet';
        }

        return (($item['category'] ?? '') === 'work') ? 'green' : 'dominant';
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array{url: string, alt: string, kind: string}
     */
    public static function image(array $item): array
    {
        $path = trim((string) ($item['featured_image'] ?? ''));

        if ($path !== '') {
            return [
                'url' => self::normalizeUrl($path),
                'alt' => trim((string) ($item['featured_image_alt'] ?? '')) ?: (string) ($item['title'] ?? ''),
                'kind' => 'image',
            ];
        }

        return [
            'url' => route('content-visuals.show', [
                'section' => $item['section'],
                'slug' => $item['slug'],
            ]),
            'alt' => trim((string) ($item['featured_image_alt'] ?? '')) ?: (string) ($item['title'] ?? ''),
            'kind' => 'placeholder',
        ];
    }

    /**
     * @param  array<string, mixed>  $item
     */
    public static function placeholderSvg(array $item): string
    {
        // Flat paper and ink, like the site (specs/018-site-refresh): no gradient,
        // no blur. Case studies take the path blue, the rest stay in ink. The tone
        // keys in content frontmatter are unchanged but no longer pick a colour.
        $isCase = ($item['section'] ?? 'writing') === 'case-studies';
        $palette = [
            'bg' => '#f5f4f0',
            'ink' => '#121212',
            'muted' => '#5c5c57',
            'accent' => $isCase ? '#1f4fd1' : '#121212',
            'line' => 'rgba(18, 18, 18, 0.16)',
        ];

        $slug = Str::of((string) ($item['slug'] ?? 'content'))
            ->replace('-', ' ')
            ->upper()
            ->limit(30, '')
            ->toString();

        $title = Str::of((string) ($item['title'] ?? 'Untitled'))
            ->squish()
            ->limit(80, '')
            ->toString();
        $summary = Str::of((string) ($item['summary'] ?? ''))
            ->squish()
            ->limit(92, '')
            ->toString();
        $titleLines = self::wrapTextLines($title, 24, 4);
        $summaryLines = $summary !== ''
            ? self::wrapTextLines($summary, 38, 3)
            : [];
        $titleMarkup = collect($titleLines)
            ->values()
            ->map(function (string $line, int $index): string {
                $dy = $index === 0 ? '0' : '38';

                return sprintf(
                    '<tspan x="86" dy="%s">%s</tspan>',
                    $dy,
                    e($line),
                );
            })
            ->implode('');
        $summaryMarkup = collect($summaryLines)
            ->values()
            ->map(function (string $line, int $index): string {
                $dy = $index === 0 ? '0' : '28';

                return sprintf(
                    '<tspan x="88" dy="%s">%s</tspan>',
                    $dy,
                    e($line),
                );
            })
            ->implode('');

        $safeSlug = e($slug);
        $safeTitle = e($title);

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 630" role="img" aria-labelledby="title desc">
  <title id="title">{$safeTitle}</title>
  <desc id="desc">Placeholder visual for {$safeTitle}</desc>
  <defs>
    <clipPath id="titleClip">
      <rect x="86" y="174" width="508" height="176" />
    </clipPath>
    <clipPath id="summaryClip">
      <rect x="88" y="386" width="520" height="120" />
    </clipPath>
  </defs>
  <rect width="1200" height="630" fill="{$palette['bg']}" />
  <rect x="24" y="24" width="1152" height="582" fill="none" stroke="{$palette['ink']}" stroke-width="2" />
  <g>
    <path d="M24 120 H1176" stroke="{$palette['line']}" stroke-width="2" />
    <path d="M24 300 H1176" stroke="{$palette['line']}" stroke-width="2" />
    <path d="M24 480 H1176" stroke="{$palette['line']}" stroke-width="2" />
    <path d="M700 24 V606" stroke="{$palette['line']}" stroke-width="2" />
  </g>
  <rect x="760" y="210" width="60" height="44" fill="{$palette['bg']}" stroke="{$palette['ink']}" stroke-width="2" />
  <path d="M826 232 L940 232" stroke="{$palette['accent']}" stroke-width="2" stroke-dasharray="10 8" />
  <rect x="946" y="210" width="60" height="44" fill="{$palette['accent']}" stroke="{$palette['accent']}" stroke-width="2" />
  <text x="88" y="100" fill="{$palette['muted']}" font-family="DM Mono, Courier New, monospace" font-size="18" letter-spacing="2.4">{$safeSlug}</text>
  <text x="86" y="214" fill="{$palette['ink']}" font-family="Switzer, Helvetica Neue, Arial, sans-serif" font-size="30" font-weight="600" clip-path="url(#titleClip)">{$titleMarkup}</text>
  <text x="88" y="406" fill="{$palette['muted']}" font-family="Switzer, Helvetica Neue, Arial, sans-serif" font-size="18" clip-path="url(#summaryClip)">{$summaryMarkup}</text>
</svg>
SVG;
    }

    /**
     * @return array<int, string>
     */
    protected static function wrapTextLines(string $text, int $maxCharacters, int $maxLines): array
    {
        $words = preg_split('/\s+/u', trim($text)) ?: [];

        if ($words === []) {
            return ['Untitled'];
        }

        $lines = [];
        $currentLine = '';

        foreach ($words as $index => $word) {
            $candidate = trim($currentLine === '' ? $word : "{$currentLine} {$word}");

            if (Str::length($candidate) <= $maxCharacters || $currentLine === '') {
                $currentLine = $candidate;

                continue;
            }

            $lines[] = $currentLine;

            if (count($lines) === $maxLines - 1) {
                $remaining = trim(implode(' ', array_slice($words, $index)));
                $lines[] = Str::of($remaining)->limit($maxCharacters, '')->toString();

                return $lines;
            }

            $currentLine = $word;
        }

        if ($currentLine !== '' && count($lines) < $maxLines) {
            $lines[] = $currentLine;
        }

        return array_slice($lines, 0, $maxLines);
    }

    protected static function normalizeUrl(string $path): string
    {
        if (Str::startsWith($path, ['http://', 'https://', 'data:'])) {
            return $path;
        }

        return url('/'.ltrim($path, '/'));
    }
}
