<?php

namespace App\Content;

use App\Support\PublicLocale;
use Illuminate\Http\Request;

/**
 * Whether this request should be served an unpublished draft.
 *
 * The preview is the real route, rendered with the draft instead of the
 * published content. That is a deliberate choice over an in-editor mock: for
 * a site whose argument is how it looks, a preview that is not the page
 * cannot answer the question the operator is asking.
 *
 * Serving unpublished content from a public URL is exactly the kind of thing
 * that leaks, so the two conditions are stated in one place and both have to
 * hold:
 *
 * - the request asks for it, with `?preview=1`;
 * - and whoever is asking is signed in to the admin.
 *
 * An anonymous visitor appending `?preview=1` gets the published page, and
 * the draft is never rendered, never cached under that URL, and never
 * exported — the static export runs against a server it starts itself, with
 * no session at all.
 */
final class ContentPreview
{
    public const QUERY_PARAMETER = 'preview';

    public static function isRequested(?Request $request = null): bool
    {
        $request ??= request();

        if (! $request instanceof Request) {
            return false;
        }

        return $request->boolean(self::QUERY_PARAMETER) && auth()->check();
    }

    /**
     * The URL that shows a draft of this page, in the language it is written
     * in. It is the public route, which is the whole point.
     */
    public static function url(string $path, string $locale): string
    {
        return PublicLocale::localizedPath($path, $locale)
            .'?'.self::QUERY_PARAMETER.'=1';
    }
}
