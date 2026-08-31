<?php

namespace Tests\Feature;

use App\Services\SiteSettingsService;
use App\Support\PublicLocale;
use App\Support\Seo;
use Tests\TestCase;

/**
 * The site name is appended to the page title exactly once.
 *
 * It used to be appended twice, and the two halves disagreed. `Seo::page()`
 * composes `"{page} · {suffix}"` in PHP, where the suffix comes from
 * `lang/{locale}/site.php` and is therefore spelled the way the locale
 * spells it — `Ismaël` in French. The client entrypoints then declared a
 * second composer:
 *
 *     title: (title) => (title ? `${title} | ${appName}`),
 *
 * where `appName` was `VITE_APP_NAME`, frozen into the bundle at build time
 * from a single `.env` line and blind to the locale — `Ismael`, no diaeresis.
 *
 * `SeoMeta.vue` hands Inertia the already-composed PHP title inside a
 * `<Head>` slot. Inertia's head manager matches any `<title …>` element it
 * collects and runs the *inner text* through that callback, so a French page
 * settled on `Journal · Ismaël Rodmacq | Ismael Rodmacq` — the name twice,
 * spelled two ways.
 *
 * Nothing caught it, because nothing could: the doubling happens when the
 * head manager commits in the browser, and every title assertion in this
 * suite reads the Blade document, which was correct all along.
 *
 * What this test pins, therefore, is the shape of the fix rather than the
 * rendered page — the suite has no browser. Two claims:
 *
 * 1. PHP composes one suffix, and only one;
 * 2. the client entrypoints compose none, so PHP stays the only composer.
 *
 * Claim 2 is enforced by reading the entrypoints, in the manner of
 * `SiteIsAgnosticTest`. The strings it forbids are the exact two ingredients
 * the old composer needed: a `title` option on `createInertiaApp`, and the
 * build-time application name. Omitting the option is enough — Inertia's
 * `titleCallback` prop already defaults to `(title) => title`.
 */
class PageTitleIsComposedOnceTest extends TestCase
{
    /**
     * The two files that boot Inertia, one per rendering environment. A
     * composer reintroduced in either of them reaches the reader.
     */
    protected const CLIENT_ENTRYPOINTS = [
        'resources/js/app.ts',
        'resources/js/ssr.ts',
    ];

    public function test_php_appends_the_site_name_once_in_every_locale(): void
    {
        foreach (PublicLocale::supported() as $locale) {
            $this->app->setLocale($locale);

            $suffix = app(SiteSettingsService::class)->current()->seoDefaults->titleSuffix;
            $seo = Seo::page('Journal', 'A description.', '/journal');

            $this->assertSame(
                1,
                substr_count($seo['title'], $suffix),
                "The {$locale} title should name the site once, and carries: {$seo['title']}",
            );

            foreach (['openGraph', 'twitter'] as $channel) {
                $this->assertSame(
                    $seo['title'],
                    $seo[$channel]['title'],
                    "The {$channel} title should be the same string the document title is.",
                );
            }
        }
    }

    public function test_the_client_entrypoints_do_not_append_a_second_site_name(): void
    {
        foreach (self::CLIENT_ENTRYPOINTS as $entrypoint) {
            $source = (string) file_get_contents(base_path($entrypoint));

            $this->assertDoesNotMatchRegularExpression(
                '/^\s*title:\s/m',
                $source,
                "{$entrypoint} declares a title callback. The page title is composed in "
                    .'App\Support\Seo, which knows the locale; a second composer here cannot.',
            );

            $this->assertStringNotContainsString(
                'VITE_APP_NAME',
                $source,
                "{$entrypoint} reads the build-time application name. It is frozen at build "
                    .'time and spelled for one locale, so no visitor-facing string may be built from it.',
            );
        }
    }
}
