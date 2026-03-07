<?php

namespace App\Support;

use Carbon\CarbonImmutable;

class Seo
{
    public static function page(string $title, string $description, string $path, array $options = []): array
    {
        $siteName = config('site.name');
        $siteUrl = rtrim((string) config('site.url'), '/');
        $canonical = $path === '/' ? $siteUrl : $siteUrl.$path;
        $fullTitle = $title === $siteName ? $siteName : sprintf('%s | %s', $title, $siteName);
        $author = config('site.author');

        $schemas = [
            [
                '@context' => 'https://schema.org',
                '@type' => 'Person',
                'name' => $author['name'],
                'jobTitle' => $author['job_title'],
                'url' => $siteUrl,
                'sameAs' => $author['same_as'],
            ],
            [
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'name' => $siteName,
                'url' => $siteUrl,
                'description' => config('site.description'),
                'inLanguage' => config('site.locale'),
            ],
        ];

        $pageSchema = [
            '@context' => 'https://schema.org',
            '@type' => $options['schema_type'] ?? 'WebPage',
            'name' => $title,
            'description' => $description,
            'url' => $canonical,
            'inLanguage' => config('site.locale'),
        ];

        if (isset($options['published_at'])) {
            $pageSchema['datePublished'] = CarbonImmutable::parse($options['published_at'])->toAtomString();
        }

        if (isset($options['updated_at'])) {
            $pageSchema['dateModified'] = CarbonImmutable::parse($options['updated_at'])->toAtomString();
        }

        if (isset($options['section'])) {
            $pageSchema['articleSection'] = $options['section'];
        }

        $schemas[] = $pageSchema;

        if (! empty($options['breadcrumb'])) {
            $schemas[] = [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => collect($options['breadcrumb'])
                    ->values()
                    ->map(fn (array $item, int $index): array => [
                        '@type' => 'ListItem',
                        'position' => $index + 1,
                        'name' => $item['name'],
                        'item' => $siteUrl.$item['path'],
                    ])
                    ->all(),
            ];
        }

        return [
            'title' => $fullTitle,
            'description' => $description,
            'canonical' => $canonical,
            'robots' => $options['robots'] ?? 'index,follow',
            'openGraph' => [
                'title' => $fullTitle,
                'description' => $description,
                'type' => $options['open_graph_type'] ?? 'website',
                'url' => $canonical,
                'site_name' => $siteName,
                'locale' => config('site.locale'),
            ],
            'twitter' => [
                'card' => 'summary_large_image',
                'title' => $fullTitle,
                'description' => $description,
            ],
            'jsonLd' => $schemas,
        ];
    }
}
