<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PublicLocale
{
    public const COOKIE_NAME = 'sidewalk_locale';

    /**
     * @return array<int, string>
     */
    public static function supported(): array
    {
        return ['en', 'fr'];
    }

    public static function default(): string
    {
        return 'en';
    }

    public static function resolveSupportedLocale(mixed $candidate): ?string
    {
        if (! is_string($candidate) || $candidate === '') {
            return null;
        }

        $normalized = strtolower(trim(explode(',', $candidate)[0]));
        $language = explode('-', $normalized)[0];

        return in_array($language, self::supported(), true)
            ? $language
            : null;
    }

    public static function localePrefix(string $locale): string
    {
        return '/'.trim($locale, '/');
    }

    public static function localizedPath(string $path, ?string $locale = null): string
    {
        if (preg_match('/^https?:\/\//i', $path) === 1) {
            return $path;
        }

        $locale ??= app()->getLocale() ?: self::default();
        $parsed = parse_url($path);
        $normalizedPath = self::stripLocaleFromPath($parsed['path'] ?? '/');
        $localized = self::localePrefix($locale);

        if ($normalizedPath !== '/') {
            $localized .= $normalizedPath;
        }

        $query = isset($parsed['query']) && $parsed['query'] !== ''
            ? '?'.$parsed['query']
            : '';
        $fragment = isset($parsed['fragment']) && $parsed['fragment'] !== ''
            ? '#'.$parsed['fragment']
            : '';

        return $localized.$query.$fragment;
    }

    public static function pathForRequest(Request $request): string
    {
        return self::stripLocaleFromPath('/'.ltrim($request->path(), '/'));
    }

    public static function preferredLocaleForRequest(Request $request): string
    {
        return self::resolveSupportedLocale($request->route('locale'))
            ?? self::resolveSupportedLocale($request->query('lang'))
            ?? self::resolveSupportedLocale($request->cookie(self::COOKIE_NAME))
            ?? self::resolveBrowserLocale($request)
            ?? self::default();
    }

    public static function pageKeyForRequest(Request $request): ?string
    {
        return match ($request->route()?->getName()) {
            'home' => 'home',
            'experience' => 'experience',
            'sparkle' => 'sparkle',
            'local' => 'local',
            'projects' => 'projects',
            'contact' => 'contact',
            'data-processing' => 'data-processing',
            default => null,
        };
    }

    public static function writingSlugForRequest(Request $request): ?string
    {
        return match ($request->route()?->getName()) {
            'writing.show', 'writing.legacy.show' => (string) $request->route('slug'),
            default => null,
        };
    }

    public static function caseStudySlugForRequest(Request $request): ?string
    {
        return match ($request->route()?->getName()) {
            'case-studies.show' => (string) $request->route('slug'),
            default => null,
        };
    }

    /**
     * @return array<int, array{label: string, href: string}>
     */
    public static function navigation(string $locale): array
    {
        $labels = $locale === 'fr'
            ? [
                '/' => 'Hello',
                '/local' => 'Localisation',
                '/projects' => 'Expériences',
                '/journal' => 'Journal',
                '/contact' => 'Contact ✍🏽',
            ]
            : [];

        return collect(config('site.navigation'))
            ->map(fn (array $item): array => [
                'label' => $labels[$item['href']] ?? $item['label'],
                'href' => self::localizedPath($item['href'], $locale),
            ])
            ->all();
    }

    /**
     * @return array{
     *     headerTagline: string,
     *     localeSwitcherLabel: string,
     *     navAriaLabel: string,
     *     navMenuLabel: string,
     *     navFallbackLabel: string,
     *     navCurrentLabel: string,
     *     navOpenLabel: string,
     *     footerNote: string,
     *     privacyControlsLabel: string
     * }
     */
    public static function shellCopy(string $locale): array
    {
        if ($locale === 'fr') {
            return [
                'headerTagline' => 'Développeur e-commerce full stack. Data transverse, flux fiables.',
                'localeSwitcherLabel' => 'Langue',
                'navAriaLabel' => 'Navigation principale',
                'navMenuLabel' => 'Menu',
                'navFallbackLabel' => 'Navigation',
                'navCurrentLabel' => 'Actif',
                'navOpenLabel' => 'Lire plus',
                'footerNote' => "Développement web, donnée produit, connecteurs, outils internes et SEO technique pour des équipes qui ont déjà du réel à faire tourner.",
                'privacyControlsLabel' => 'Réglages vie privée',
            ];
        }

        return [
            'headerTagline' => 'Full-stack e-commerce. Cross-functional data, reliable flows.',
            'localeSwitcherLabel' => 'Language',
            'navAriaLabel' => 'Primary navigation',
            'navMenuLabel' => 'Menu',
            'navFallbackLabel' => 'Navigation',
            'navCurrentLabel' => 'Current',
            'navOpenLabel' => 'Read more',
            'footerNote' => 'Web engineering for product data, integrations, internal tools, and technical SEO in teams already running real operations.',
            'privacyControlsLabel' => 'Privacy controls',
        ];
    }

    public static function isAvailableForRequest(Request $request, string $locale): bool
    {
        if ($locale === self::default()) {
            return true;
        }

        $pageKey = self::pageKeyForRequest($request);

        if ($pageKey !== null) {
            return File::exists(resource_path("content/pages/{$locale}/{$pageKey}.md"));
        }

        return match ($request->route()?->getName()) {
            'writing.index' => self::localizedCollectionExists('writing', $locale),
            'writing.show' => self::localizedCollectionSlugExists(
                'writing',
                self::writingSlugForRequest($request),
                $locale,
            ),
            'case-studies.index' => self::localizedCollectionExists('case-studies', $locale),
            'case-studies.show' => self::localizedCollectionSlugExists(
                'case-studies',
                self::caseStudySlugForRequest($request),
                $locale,
            ),
            default => false,
        };
    }

    protected static function localizedCollectionExists(string $section, string $locale): bool
    {
        $directory = resource_path("content/{$section}/{$locale}");

        if (! File::isDirectory($directory)) {
            return false;
        }

        return collect(File::files($directory))
            ->contains(fn ($file): bool => $file->getExtension() === 'md');
    }

    protected static function localizedCollectionSlugExists(string $section, ?string $slug, string $locale): bool
    {
        if ($slug === null || $slug === '') {
            return false;
        }

        return File::exists(resource_path("content/{$section}/{$locale}/{$slug}.md"));
    }

    /**
     * @return array{
     *     visible: bool,
     *     current: string,
     *     preferred: string,
     *     options: array<int, array{
     *         code: string,
     *         label: string,
     *         available: bool,
     *         href: string|null
     *     }>
     * }
     */
    public static function switcher(Request $request, string $currentLocale, string $preferredLocale): array
    {
        $path = self::pathForRequest($request);
        $query = collect($request->query())
            ->except(['lang', 'path'])
            ->all();

        $options = collect(self::supported())
            ->map(function (string $locale) use ($request): array {
                $available = self::isAvailableForRequest($request, $locale);

                return [
                    'code' => $locale,
                    'label' => strtoupper($locale),
                    'available' => $available,
                    'href' => null,
                ];
            })
            ->map(function (array $option) use ($path, $query): array {
                if (! $option['available']) {
                    return $option;
                }

                $href = self::localizedPath($path, $option['code']);

                if ($query !== []) {
                    $href .= '?'.http_build_query($query);
                }

                $option['href'] = $href;

                return $option;
            })
            ->values()
            ->all();

        return [
            'visible' => self::isAvailableForRequest($request, 'fr'),
            'current' => $currentLocale,
            'preferred' => $preferredLocale,
            'options' => $options,
        ];
    }

    protected static function resolveBrowserLocale(Request $request): ?string
    {
        foreach ($request->getLanguages() as $language) {
            $locale = self::resolveSupportedLocale($language);

            if ($locale !== null) {
                return $locale;
            }
        }

        return null;
    }

    protected static function stripLocaleFromPath(string $path): string
    {
        $normalized = '/'.ltrim($path, '/');
        $normalized = preg_replace('#/+#', '/', $normalized) ?: '/';

        foreach (self::supported() as $locale) {
            $prefix = self::localePrefix($locale);

            if ($normalized === $prefix) {
                return '/';
            }

            if (str_starts_with($normalized, $prefix.'/')) {
                return substr($normalized, strlen($prefix)) ?: '/';
            }
        }

        return rtrim($normalized, '/') ?: '/';
    }
}
