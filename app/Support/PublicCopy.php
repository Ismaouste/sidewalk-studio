<?php

namespace App\Support;

use Illuminate\Support\Facades\Lang;
use RuntimeException;

/**
 * Reads `lang/{locale}/public.php`, the server-resolved half of the public
 * surface's editorial copy.
 *
 * Laravel's `trans()` returns the key itself when the key is missing, so a
 * typo renders as the literal string `public.seo.journal.title` on the page —
 * or, for a group, as an empty section. That is the failure mode the
 * TypeScript copy tree exists to prevent, and it would be a poor trade to
 * reintroduce it on the way out of the controller ternaries. So every read
 * goes through here and a missing key throws.
 *
 * The pair of guarantees:
 *
 * - a key missing from one locale only is caught by `LanguageFileParityTest`
 *   before it can ship;
 * - a key missing from both, or misspelled at the call site, throws here and
 *   fails the feature test that renders the route.
 */
final class PublicCopy
{
    /**
     * @return array<string, mixed>
     */
    public static function group(string $key, ?string $locale = null): array
    {
        $value = self::read($key, $locale);

        if (! is_array($value)) {
            throw new RuntimeException(
                "Public copy key [public.{$key}] is a line, not a group.",
            );
        }

        return $value;
    }

    public static function line(string $key, ?string $locale = null): string
    {
        $value = self::read($key, $locale);

        if (! is_string($value)) {
            throw new RuntimeException(
                "Public copy key [public.{$key}] is a group, not a line.",
            );
        }

        return $value;
    }

    protected static function read(string $key, ?string $locale): mixed
    {
        $locale ??= app()->getLocale();
        $namespaced = "public.{$key}";

        if (! Lang::has($namespaced, $locale)) {
            throw new RuntimeException(
                "Missing public copy key [{$namespaced}] for locale [{$locale}].",
            );
        }

        return Lang::get($namespaced, [], $locale);
    }
}
