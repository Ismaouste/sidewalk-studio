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
            default => null,
        };
    }

    public static function isAvailableForRequest(Request $request, string $locale): bool
    {
        if ($locale === self::default()) {
            return true;
        }

        $pageKey = self::pageKeyForRequest($request);

        if ($pageKey === null) {
            return false;
        }

        return File::exists(resource_path("content/pages/{$locale}/{$pageKey}.md"));
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
