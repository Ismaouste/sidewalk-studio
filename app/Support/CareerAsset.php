<?php

namespace App\Support;

use App\Services\SiteSettingsService;
use Illuminate\Support\Str;

/**
 * Where the CV lives, and what it is called when a visitor saves it.
 *
 * Those are two different questions, and conflating them is why the owner's
 * name was hard-coded in six places: the file on disk was named after the
 * person, so every path that touched it named the person too.
 *
 * They separate cleanly:
 *
 * - **On disk** the file is addressed by a setting — a directory and a
 *   pattern — and its name says nothing about who it belongs to. A fork
 *   drops its own PDF in and changes nothing else.
 * - **On download** the name is built from the site identity at request
 *   time, because `cv-en.pdf` sitting in a downloads folder among thirty
 *   other files is useless to the person who saved it.
 */
final class CareerAsset
{
    public static function sourcePath(string $locale): string
    {
        return base_path(
            trim((string) config('site.cv.directory'), '/')
            .'/'
            .self::sourceName($locale),
        );
    }

    public static function exists(string $locale): bool
    {
        return is_file(self::sourcePath($locale));
    }

    /**
     * What the browser saves it as. Derived from the identity so a renamed
     * site cannot keep handing out a file named after the previous owner.
     */
    public static function downloadName(string $locale): string
    {
        $slug = Str::slug(
            app(SiteSettingsService::class)->current($locale)->siteIdentity->name,
        );

        return ($slug === '' ? 'cv' : "{$slug}-cv")."-{$locale}.pdf";
    }

    /**
     * Path inside the static export, relative to its root. It carries the
     * download name rather than the source name, so saving the file from the
     * static preview and from the live site produce the same thing.
     */
    public static function exportRelativePath(string $locale): string
    {
        return 'assets/cv/'.self::downloadName($locale);
    }

    protected static function sourceName(string $locale): string
    {
        return str_replace(
            '{locale}',
            $locale,
            (string) config('site.cv.filename'),
        );
    }
}
