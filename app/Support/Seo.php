<?php

namespace App\Support;

use App\Services\SiteSettingsService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\File;

class Seo
{
    public static function page(string $title, string $description, string $path, array $options = []): array
    {
        $settings = app(SiteSettingsService::class)->current();
        $siteName = $settings->siteIdentity->name;
        $siteUrl = rtrim((string) config('site.url'), '/');
        $locale = app()->getLocale();
        $author = config('site.author');
        $fullTitle = $title === $siteName ? $siteName : sprintf('%s · %s', $title, $settings->seoDefaults->titleSuffix);
        $resolvedDescription = trim($description) !== '' ? $description : $settings->seoDefaults->defaultDescription;
        $canonical = self::resolveCanonical(
            $options['canonical_url'] ?? $options['canonical'] ?? '',
            $path,
            $siteUrl,
            $locale,
        );
        $resolvedImage = self::resolveImage(
            $options['image'] ?? [],
            $siteUrl,
            $options['image_slug'] ?? ($options['image']['slug'] ?? null),
            $title,
        );
        $schemaVariant = $options['schema_variant'] ?? 'page';
        $sameAs = $settings->socialLinks->sameAs();

        $schemas = array_values(array_filter([
            self::websiteSchema(
                siteName: $siteName,
                siteUrl: $siteUrl,
                description: $settings->seoDefaults->defaultDescription,
                locale: $locale,
            ),
            self::schemaForVariant(
                variant: $schemaVariant,
                title: $title,
                description: $resolvedDescription,
                canonical: $canonical,
                locale: $locale,
                author: is_array($author) ? $author : [],
                settings: $settings,
                sameAs: $sameAs,
                options: $options,
                image: $resolvedImage,
                siteUrl: $siteUrl,
            ),
            self::breadcrumbSchema(
                breadcrumb: $options['breadcrumb'] ?? [],
                locale: $locale,
                siteUrl: $siteUrl,
            ),
        ]));

        return [
            'title' => $fullTitle,
            'description' => $resolvedDescription,
            'canonical' => $canonical,
            'robots' => $options['robots'] ?? $settings->seoDefaults->defaultRobots,
            'breadcrumbs' => collect($options['breadcrumb'] ?? [])
                ->values()
                ->map(fn (array $item): array => [
                    'label' => $item['name'],
                    'href' => PublicLocale::localizedPath($item['path'], $locale),
                ])
                ->all(),
            'openGraph' => [
                'title' => $fullTitle,
                'description' => $resolvedDescription,
                'type' => $options['open_graph_type'] ?? ($schemaVariant === 'article' ? 'article' : 'website'),
                'url' => $canonical,
                'site_name' => $siteName,
                'locale' => $locale,
                'image' => $resolvedImage['url'],
                'image_alt' => $resolvedImage['alt'],
            ],
            'twitter' => [
                'card' => 'summary_large_image',
                'title' => $fullTitle,
                'description' => $resolvedDescription,
                'image' => $resolvedImage['url'],
                'image_alt' => $resolvedImage['alt'],
            ],
            'jsonLd' => $schemas,
        ];
    }

    /**
     * @param  array<string, mixed>  $author
     * @param  array<int, string>  $sameAs
     * @param  array<string, mixed>  $options
     * @param  array{url: string, alt: string}  $image
     * @return array<string, mixed>|null
     */
    protected static function schemaForVariant(
        string $variant,
        string $title,
        string $description,
        string $canonical,
        string $locale,
        array $author,
        mixed $settings,
        array $sameAs,
        array $options,
        array $image,
        string $siteUrl,
    ): ?array {
        return match ($variant) {
            'article' => self::articleSchema(
                title: $title,
                description: $description,
                canonical: $canonical,
                locale: $locale,
                author: $author,
                sameAs: $sameAs,
                publishedAt: $options['published_at'] ?? null,
                updatedAt: $options['updated_at'] ?? null,
                image: $image,
                keywords: $options['keywords'] ?? [],
                siteUrl: $siteUrl,
            ),
            'creative_work' => self::creativeWorkSchema(
                title: $title,
                description: $description,
                canonical: $canonical,
                locale: $locale,
                author: $author,
                publishedAt: $options['published_at'] ?? null,
                image: $image,
                keywords: $options['keywords'] ?? [],
            ),
            'person_surface' => self::personSurfaceSchema(
                author: $author,
                settings: $settings,
                sameAs: $sameAs,
                knowsAbout: $options['person']['knows_about'] ?? [],
                email: $options['person']['email'] ?? $settings->contactDetails->email,
                jobTitle: $options['person']['job_title'] ?? null,
                siteUrl: $siteUrl,
            ),
            default => self::webPageSchema(
                title: $title,
                description: $description,
                canonical: $canonical,
                locale: $locale,
                section: $options['section'] ?? null,
                publishedAt: $options['published_at'] ?? null,
                updatedAt: $options['updated_at'] ?? null,
                image: $image,
                schemaType: $options['schema_type'] ?? 'WebPage',
            ),
        };
    }

    protected static function websiteSchema(string $siteName, string $siteUrl, string $description, string $locale): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $siteName,
            'url' => $siteUrl,
            'description' => $description,
            'inLanguage' => $locale,
        ];
    }

    /**
     * @param  array{url: string, alt: string}  $image
     * @return array<string, mixed>
     */
    protected static function webPageSchema(
        string $title,
        string $description,
        string $canonical,
        string $locale,
        ?string $section,
        mixed $publishedAt,
        mixed $updatedAt,
        array $image,
        string $schemaType,
    ): array {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => $schemaType,
            'name' => $title,
            'description' => $description,
            'url' => $canonical,
            'inLanguage' => $locale,
        ];

        if ($section !== null && trim($section) !== '') {
            $schema['articleSection'] = $section;
        }

        if ($publishedAt !== null) {
            $schema['datePublished'] = self::toAtomString($publishedAt);
        }

        if ($updatedAt !== null) {
            $schema['dateModified'] = self::toAtomString($updatedAt);
        }

        if ($image['url'] !== '') {
            $schema['image'] = $image['url'];
        }

        return $schema;
    }

    /**
     * @param  array<string, mixed>  $author
     * @param  array<int, string>  $sameAs
     * @param  array<int, string>  $keywords
     * @param  array{url: string, alt: string}  $image
     * @return array<string, mixed>
     */
    protected static function articleSchema(
        string $title,
        string $description,
        string $canonical,
        string $locale,
        array $author,
        array $sameAs,
        mixed $publishedAt,
        mixed $updatedAt,
        array $image,
        array $keywords,
        string $siteUrl,
    ): array {
        $authorName = (string) ($author['name'] ?? 'Ismael Rodmacq');
        $published = self::toAtomString($publishedAt);
        $updated = $updatedAt !== null ? self::toAtomString($updatedAt) : $published;

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $title,
            'description' => $description,
            'datePublished' => $published,
            'dateModified' => $updated,
            'author' => [
                '@type' => 'Person',
                'name' => $authorName,
                'url' => $siteUrl,
                'sameAs' => $sameAs,
            ],
            'publisher' => [
                '@type' => 'Person',
                'name' => $authorName,
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $canonical,
            ],
            'inLanguage' => $locale,
        ];

        if ($image['url'] !== '') {
            $schema['image'] = [$image['url']];
        }

        if ($keywords !== []) {
            $schema['keywords'] = implode(', ', $keywords);
        }

        return $schema;
    }

    /**
     * @param  array<string, mixed>  $author
     * @param  array<int, string>  $keywords
     * @param  array{url: string, alt: string}  $image
     * @return array<string, mixed>
     */
    protected static function creativeWorkSchema(
        string $title,
        string $description,
        string $canonical,
        string $locale,
        array $author,
        mixed $publishedAt,
        array $image,
        array $keywords,
    ): array {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'CreativeWork',
            'name' => $title,
            'description' => $description,
            'author' => [
                '@type' => 'Person',
                'name' => (string) ($author['name'] ?? 'Ismael Rodmacq'),
            ],
            'url' => $canonical,
            'inLanguage' => $locale,
        ];

        if ($publishedAt !== null) {
            $schema['dateCreated'] = CarbonImmutable::parse((string) $publishedAt)->toDateString();
        }

        if ($keywords !== []) {
            $schema['keywords'] = implode(', ', $keywords);
        }

        if ($image['url'] !== '') {
            $schema['image'] = [$image['url']];
        }

        return $schema;
    }

    /**
     * @param  array<string, mixed>  $author
     * @param  array<int, string>  $sameAs
     * @param  array<int, string>  $knowsAbout
     * @return array<string, mixed>
     */
    protected static function personSurfaceSchema(
        array $author,
        mixed $settings,
        array $sameAs,
        array $knowsAbout,
        ?string $email,
        ?string $jobTitle,
        string $siteUrl,
    ): array {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => (string) ($author['name'] ?? 'Ismael Rodmacq'),
            'jobTitle' => $jobTitle ?: (string) ($author['job_title'] ?? 'Full Stack Developer — E-commerce & Product Data'),
            'url' => $siteUrl,
            'sameAs' => $sameAs,
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => 'Nancy',
                'addressRegion' => 'Grand Est',
                'addressCountry' => 'FR',
            ],
            'knowsAbout' => $knowsAbout,
        ];

        if (is_string($email) && trim($email) !== '') {
            $schema['email'] = trim($email);
        }

        return $schema;
    }

    /**
     * @param  array<int, array{name: string, path: string}>  $breadcrumb
     * @return array<string, mixed>|null
     */
    protected static function breadcrumbSchema(array $breadcrumb, string $locale, string $siteUrl): ?array
    {
        if ($breadcrumb === []) {
            return null;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($breadcrumb)
                ->values()
                ->map(fn (array $item, int $index): array => [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'name' => $item['name'],
                    'item' => $siteUrl.PublicLocale::localizedPath($item['path'], $locale),
                ])
                ->all(),
        ];
    }

    protected static function resolveCanonical(string $explicitCanonical, string $path, string $siteUrl, string $locale): string
    {
        if (trim($explicitCanonical) !== '') {
            return self::absoluteUrl($explicitCanonical, $siteUrl);
        }

        return self::absoluteUrl(PublicLocale::localizedPath($path, $locale), $siteUrl);
    }

    /**
     * @param  array<string, mixed>  $image
     * @return array{url: string, alt: string}
     */
    protected static function resolveImage(array $image, string $siteUrl, ?string $slug, string $title): array
    {
        $alt = trim((string) ($image['alt'] ?? ''));
        $alt = $alt !== '' ? $alt : $title;

        $candidates = array_values(array_filter([
            trim((string) ($image['url'] ?? '')),
            is_string($slug) && $slug !== '' ? "/images/og/{$slug}.jpg" : null,
            '/images/og/site-default.jpg',
        ]));

        foreach ($candidates as $candidate) {
            if ($candidate === '') {
                continue;
            }

            if (preg_match('/^https?:\/\//i', $candidate) === 1) {
                return ['url' => $candidate, 'alt' => $alt];
            }

            $normalized = '/'.ltrim($candidate, '/');

            if (File::exists(public_path(ltrim($normalized, '/')))) {
                return ['url' => self::absoluteUrl($normalized, $siteUrl), 'alt' => $alt];
            }
        }

        return ['url' => '', 'alt' => $alt];
    }

    protected static function absoluteUrl(string $candidate, string $siteUrl): string
    {
        $candidate = self::replaceSiteUrlPlaceholder($candidate, $siteUrl);

        if (preg_match('/^https?:\/\//i', $candidate) === 1) {
            return $candidate;
        }

        return $siteUrl.'/'.ltrim($candidate, '/');
    }

    protected static function replaceSiteUrlPlaceholder(string $candidate, string $siteUrl): string
    {
        $normalized = trim($candidate);

        foreach (self::siteUrlPlaceholders() as $placeholder) {
            if ($placeholder !== '' && str_contains($normalized, $placeholder)) {
                $normalized = str_replace($placeholder, $siteUrl, $normalized);
            }
        }

        return $normalized;
    }

    /**
     * @return array<int, string>
     */
    protected static function siteUrlPlaceholders(): array
    {
        return array_values(array_filter(array_unique([
            (string) config('site.url_placeholder', '{{site_url}}'),
            '{{site_url}}',
            '{site_url}',
            '__SITE_URL__',
        ])));
    }

    protected static function toAtomString(mixed $value): string
    {
        return CarbonImmutable::parse((string) $value)->toAtomString();
    }
}
