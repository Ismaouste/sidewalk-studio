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

    public static function pageKeyForRequest(Request $request): ?string
    {
        return match ($request->route()?->getName()) {
            'home' => 'home',
            'experience' => 'experience',
            'local' => 'local',
            'projects' => 'projects',
            'contact' => 'contact',
            default => null,
        };
    }

    public static function writingSlugForRequest(Request $request): ?string
    {
        return match ($request->route()?->getName()) {
            'writing.show' => (string) $request->route('slug'),
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
                '/' => 'Accueil',
                '/experience' => 'Experience',
                '/local' => 'Local',
                '/projects' => 'Projets',
                '/writing' => 'Notes',
                '/contact' => 'Contact',
            ]
            : [];

        return collect(config('site.navigation'))
            ->map(fn (array $item): array => [
                'label' => $labels[$item['href']] ?? $item['label'],
                'href' => $item['href'],
            ])
            ->all();
    }

    /**
     * @return array{
     *     localeSwitcherLabel: string,
     *     navAriaLabel: string,
     *     navMenuLabel: string,
     *     navFallbackLabel: string,
     *     navCurrentLabel: string,
     *     navOpenLabel: string,
     *     footerDividerLabel: string,
     *     footerNote: string,
     *     privacyControlsLabel: string
     * }
     */
    public static function shellCopy(string $locale): array
    {
        if ($locale === 'fr') {
            return [
                'localeSwitcherLabel' => 'Langue',
                'navAriaLabel' => 'Navigation principale',
                'navMenuLabel' => 'Menu',
                'navFallbackLabel' => 'Navigation',
                'navCurrentLabel' => 'Actif',
                'navOpenLabel' => 'Ouvrir',
                'footerDividerLabel' => 'Journal public',
                'footerNote' => 'Portfolio Laravel local-first. Embeds soumis au consentement. Contenu structure. Shell pret pour le SSR.',
                'privacyControlsLabel' => 'Reglages vie privee',
            ];
        }

        return [
            'localeSwitcherLabel' => 'Language',
            'navAriaLabel' => 'Primary navigation',
            'navMenuLabel' => 'Menu',
            'navFallbackLabel' => 'Navigation',
            'navCurrentLabel' => 'Current',
            'navOpenLabel' => 'Open',
            'footerDividerLabel' => 'Public build log',
            'footerNote' => 'Local-first Laravel portfolio. Consent-aware embeds. Structured content. SSR-ready shell.',
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
        $options = collect(self::supported())
            ->map(function (string $locale) use ($request): array {
                $available = self::isAvailableForRequest($request, $locale);

                return [
                    'code' => $locale,
                    'label' => strtoupper($locale),
                    'available' => $available,
                    'href' => $available
                        ? $request->fullUrlWithQuery(['lang' => $locale])
                        : null,
                ];
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
}
