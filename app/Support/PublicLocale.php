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
            'experience' => 'projects',
            'services' => 'services',
            'sparkle' => 'sparkle',
            'local' => 'local',
            'contact' => 'contact',
            'data-processing' => 'data-processing',
            'colophon' => 'colophon',
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
     * `$currentPath` is a locale-stripped path, as returned by
     * `pathForRequest()`. Resolving the active entry here rather than in the
     * client keeps one implementation of the rule: the routing table already
     * lives on this side, and a second copy in TypeScript can only drift from
     * it.
     *
     * `path` is the locale-stripped route the entry points at. It is the key
     * the client copy tables are indexed by, so shipping it removes the last
     * reason for the client to take a localized href apart.
     *
     * @return array<int, array{label: string, href: string, path: string, active: bool}>
     */
    public static function navigation(string $locale, ?string $currentPath = null): array
    {
        $labels = PublicCopy::group('navigation', $locale);

        $current = self::normalizeSectionPath($currentPath ?? '/');

        return collect(config('site.navigation'))
            ->map(fn (array $item): array => [
                'label' => $labels[$item['href']] ?? $item['label'],
                'href' => self::localizedPath($item['href'], $locale),
                'path' => self::normalizeSectionPath($item['href']),
                'active' => self::isActiveSection($current, $item['href']),
            ])
            ->all();
    }

    /**
     * What the site calls its own front page, taken from the navigation so the
     * two cannot disagree. Ten controller call sites used to spell it out as
     * "Home" / "Accueil", which is not what the navigation has said for a
     * while — and a breadcrumb that names a page differently from the menu
     * pointing at it is a breadcrumb the reader has to translate.
     */
    public static function homeLabel(string $locale): string
    {
        foreach (self::navigation($locale) as $entry) {
            if ($entry['path'] === '/') {
                return $entry['label'];
            }
        }

        return 'Home';
    }

    /**
     * A section owns its whole subtree, so `/journal` stays lit on
     * `/journal/<slug>`. Home is the exception: it owns only itself, or the
     * prefix rule would light it up on every page.
     */
    protected static function isActiveSection(string $currentPath, string $sectionHref): bool
    {
        $section = self::normalizeSectionPath($sectionHref);

        if ($section === '/') {
            return $currentPath === '/';
        }

        return $currentPath === $section
            || str_starts_with($currentPath, $section.'/');
    }

    protected static function normalizeSectionPath(string $path): string
    {
        $normalized = self::stripLocaleFromPath($path);

        return rtrim($normalized, '/') ?: '/';
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
    /**
     * The keys cross a language boundary here: `lang/` is snake_case by
     * convention and `SiteProps.shell` is camelCase because TypeScript reads
     * it. Mapping them one by one rather than transforming the case keeps the
     * contract between the two readable in one screen — and makes a key added
     * on one side without the other a type error rather than a blank label.
     *
     * @return array<string, string>
     */
    public static function shellCopy(string $locale): array
    {
        $copy = PublicCopy::group('shell', $locale);

        return [
            'headerTagline' => $copy['header_tagline'],
            'localeSwitcherLabel' => $copy['locale_switcher_label'],
            'navAriaLabel' => $copy['nav_aria_label'],
            'navMenuLabel' => $copy['nav_menu_label'],
            'navFallbackLabel' => $copy['nav_fallback_label'],
            'navCurrentLabel' => $copy['nav_current_label'],
            'navOpenLabel' => $copy['nav_open_label'],
            'footerNote' => $copy['footer_note'],
            'privacyControlsLabel' => $copy['privacy_controls_label'],
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
