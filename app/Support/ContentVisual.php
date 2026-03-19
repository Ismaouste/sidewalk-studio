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
        $tone = self::tone($item);
        $palette = match ($tone) {
            'green' => [
                'bgA' => '#0f3b2e',
                'bgB' => '#1f6d54',
                'ink' => '#e8f4ef',
                'accent' => '#b6f09c',
                'line' => 'rgba(182, 240, 156, 0.18)',
            ],
            'violet' => [
                'bgA' => '#29134f',
                'bgB' => '#4c2387',
                'ink' => '#f6e9a2',
                'accent' => '#f6e9a2',
                'line' => 'rgba(246, 233, 162, 0.16)',
            ],
            'coral' => [
                'bgA' => '#4d1714',
                'bgB' => '#8e2d26',
                'ink' => '#f9d4b8',
                'accent' => '#f7b36f',
                'line' => 'rgba(247, 179, 111, 0.16)',
            ],
            default => [
                'bgA' => '#12274b',
                'bgB' => '#2b5e99',
                'ink' => '#e9f1ff',
                'accent' => '#f2c15f',
                'line' => 'rgba(242, 193, 95, 0.18)',
            ],
        };

        $slug = Str::of((string) ($item['slug'] ?? 'content'))
            ->replace('-', ' ')
            ->upper()
            ->limit(30, '')
            ->toString();

        $title = Str::of((string) ($item['title'] ?? 'Untitled'))
            ->squish()
            ->limit(88, '')
            ->toString();
        $summary = Str::of((string) ($item['summary'] ?? ''))
            ->squish()
            ->limit(108, '')
            ->toString();
        $titleLines = self::wrapTextLines($title, 28, 3);
        $summaryLines = $summary !== ''
            ? self::wrapTextLines($summary, 56, 2)
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
    <linearGradient id="bg" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="{$palette['bgA']}" />
      <stop offset="100%" stop-color="{$palette['bgB']}" />
    </linearGradient>
    <filter id="blur">
      <feGaussianBlur stdDeviation="44" />
    </filter>
  </defs>
  <rect width="1200" height="630" rx="24" fill="url(#bg)" />
  <g opacity="0.72">
    <circle cx="940" cy="130" r="180" fill="{$palette['accent']}" filter="url(#blur)" />
    <circle cx="260" cy="520" r="220" fill="{$palette['accent']}" filter="url(#blur)" />
  </g>
  <g opacity="0.9">
    <path d="M0 120 H1200" stroke="{$palette['line']}" stroke-width="2" />
    <path d="M0 210 H1200" stroke="{$palette['line']}" stroke-width="2" />
    <path d="M0 300 H1200" stroke="{$palette['line']}" stroke-width="2" />
    <path d="M0 390 H1200" stroke="{$palette['line']}" stroke-width="2" />
    <path d="M0 480 H1200" stroke="{$palette['line']}" stroke-width="2" />
  </g>
  <rect x="72" y="78" width="288" height="38" rx="10" fill="rgba(255,255,255,0.08)" />
  <text x="90" y="103" fill="{$palette['ink']}" font-family="DM Sans, Arial, sans-serif" font-size="21" font-weight="600" letter-spacing="2.8">{$safeSlug}</text>
  <text x="86" y="234" fill="{$palette['ink']}" font-family="Fraunces, Georgia, serif" font-size="32" font-weight="400" opacity="0.97">{$titleMarkup}</text>
  <text x="88" y="382" fill="{$palette['ink']}" font-family="DM Sans, Arial, sans-serif" font-size="22" opacity="0.78">{$summaryMarkup}</text>
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
